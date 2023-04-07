<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/UsersClass.class.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/ThreadsClass.class.php';

header('Content-Type: application/json; charset=utf-8');

$dbResult = array("response" => 400, "data" => array("message" => "Fields are empty."));

foreach ($_POST as $key => $value) {
    echo $key . " = " . $value . "<br>";
}

if ($_SERVER['REQUEST_METHOD'] === "POST") {

	if (!empty($_POST['title']) && !empty($_POST['url']) && !empty($_FILES)) {
		$dbResult = (new ThreadHelperClass())->createThread([$_POST['title'], $_POST['url'], $_FILES['threadBackground'], $_FILES['threadProfile']]);
	} else if (!empty($_POST['threadUrl']) && ($_POST['dataStatus']) == 0 || ($_POST['dataStatus']) == 1) {
		$dbResult = (new ThreadHelperClass())->updateUserThreads([$_POST['threadUrl'], $_POST['dataStatus']]);
	}
} 

if ($_SERVER['REQUEST_METHOD'] === "GET") {
	if (!empty($_GET['query']) && isset($_GET['threadSearch']) && $_GET['threadSearch']) {
		$dbResult = (new ThreadHelperClass())->searchThreads([$_GET['query']]);
	} else if (empty($_GET['query'])) {
		$dbResult =(new ThreadsClass())->viewThreads();
	}
}


class ThreadHelperClass {
    
   function loginStatus() : bool {
		if (isset($_SESSION['IS_AUTHORIZED'])) return true;
		return false;
	}
	
	 function searchThreads(array $inputs) : array {
		if (!is_string($inputs[0])) return array( "response" => 400, "data" => array("message" => "Invalid information"));

		$inputQuery = filter_var($inputs[0], FILTER_SANITIZE_STRING);
		$inputQuery = trim($inputQuery);
		$inputQuery = stripslashes($inputQuery);
		$inputQuery = htmlspecialchars($inputQuery);
		
		return (new ThreadsClass())->getThreadByQuery($inputQuery);
	}

	 function createThread(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		$threadTitle = $inputs[0];
		$threadUrl = $inputs[1];
		$threadBackground = $inputs[2];
		$threadProfile = $inputs[3];

		if (empty($threadTitle)) {
			return array("response" => 400, "data" => array("message" => "Thread postTitle cannot be empty11."));
		}

		if (!preg_match("/^[a-zA-Z0-9\s]+$/", $threadTitle)) {
			return array("response" => 400, "data" => array("message" => "The thread postTitle cannot contain special characters11."));
		}

		if (strlen($threadTitle) > 12) {
			return array("response" => 400, "data" => array("message" => "The thread postTitle cannot be longer than 12 characters."));
		}

		$toCheckURL = str_replace("-", " ", $threadUrl);
		if (strtolower($toCheckURL) != strtolower($threadTitle)) {
			return array("response" => 400, "data" => array("message" => "The thread URL must match the thread title."));
		}

		if ($threadBackground['size'] == 0 || $threadProfile['size'] > (5 * 1024 * 1024)) {
			return array("response" => 400, "data" => array("message" => "The thread background image must be less than 5MB."));
		}

		if ($threadProfile['size'] == 0 || $threadProfile['size'] > (5 * 1024 * 1024)) {
			return array("response" => 400, "data" => array("message" => "The thread profile image must be less than 5MB."));
		}

		$targetDirThreadBackground = $_SERVER["DOCUMENT_ROOT"].'/server/uploads/thread_backgrounds/';
		$targetDirThreadProfile = $_SERVER["DOCUMENT_ROOT"].'/server/uploads/thread_profile/';

		$threadBackgroundFileType = strtolower(pathinfo($threadBackground['name'], PATHINFO_EXTENSION));
		$threadProfileFileType = strtolower(pathinfo($threadProfile['name'], PATHINFO_EXTENSION));

		if ($threadBackgroundFileType != "jpg" && $threadBackgroundFileType != "png" && $threadBackgroundFileType != "gif")
			return array("response" => 400, "data" => array("message" => "Only .jpg, .png, and .gif format accepted."));
		
		if ($threadProfileFileType != "jpg" && $threadProfileFileType != "png" && $threadProfileFileType != "gif")
			return array("response" => 400, "data" => array("message" => "Only .jpg, .png, and .gif format accepted."));

		$threadBackgroundFile = "";
		$threadProfileFile = "";
		$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		for ($i = 0; $i < 16; $i++) {
			$threadBackgroundFile .= $randString[mt_rand(0, 61)];
			$threadProfileFile .= $randString[mt_rand(0, 61)];
		}

		$targetThreadBackgroundFile = $targetDirThreadBackground . 
			basename($threadBackgroundFile.'.'.strtolower(pathinfo($threadBackground["name"], PATHINFO_EXTENSION)));
		
		$targetThreadProfileFile = $targetDirThreadProfile . 
			basename($threadProfileFile.'.'.strtolower(pathinfo($threadProfile["name"], PATHINFO_EXTENSION)));

			// Check if file already exists
			// Added by Aziz
			// uploads folders requires chmod -R 777 to work
			if (!is_dir($targetDirThreadBackground) && !mkdir($targetDirThreadBackground)){
				die("Error creating thread background folder $targetDirThreadBackground");
			}
			// Check if file already exists
			// Added by Aziz
			if (!is_dir($targetDirThreadProfile) && !mkdir($targetDirThreadProfile)){
				die("Error creating thread background folder $targetDirThreadProfile");
			}
		
		move_uploaded_file($threadBackground["tmp_name"], $targetThreadBackgroundFile);
		move_uploaded_file($threadProfile["tmp_name"], $targetThreadProfileFile);

		return (new ThreadsClass())->post([
			$threadTitle, $threadUrl, 
			$threadBackgroundFile.'.'.$threadBackgroundFileType, 
			$threadProfileFile.'.'.$threadProfileFileType
			]);
    }

	 function updateUserThreads(array $inputs) {
		if (!$this->loginStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		return (new ThreadsClass())->userThreadsOperations([$inputs[0], $inputs[1]]);
	}
	
}

$sqlResponse = json_encode($dbResult, true);
echo $sqlResponse;
?>
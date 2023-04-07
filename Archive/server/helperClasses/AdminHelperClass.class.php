<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/AdminClass.class.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/UsersClass.class.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/ThreadsClass.class.php';
header('Content-Type: application/json; charset=utf-8');

$dbResult = array("response" => 400, "data" => array("message" => "Fields are empty."));

if ($_SERVER['REQUEST_METHOD'] === "GET") {
	if(isset($_GET['query']) && !empty($_GET['query']) && strlen($_GET['query']) != 0) {
		$dbResult = (new AdminHelperClass())->viewUserFromUsername($_GET['query']);
	} else if (isset($_GET['query']) && empty($_GET['query']) && strlen($_GET['query']) == 0 && empty($_GET['queryThread'])) {
		$dbResult = (new AdminHelperClass())->viewUserFromUsername("");
	} else if (!empty($_GET['queryThread'])) {
		$dbResult = (new AdminHelperClass())->viewThreadFromTitle($_GET['queryThread']);
	} else if (isset($_GET['queryThread']) && empty($_GET['queryThread']) && strlen($_GET['queryThread']) == 0) {
		$dbResult = (new AdminHelperClass())->viewThreadFromTitle("");
	}
} else if ($_SERVER['REQUEST_METHOD'] === "POST") {
	if(!empty($_POST['action']) && !empty($_POST['userId'])) {
		$dbResult = (new AdminHelperClass())->updateBlockedUser([$_POST['action'], $_POST['userId']]);
	} else if (!empty($_POST['actionAdmin']) && !empty($_POST['userId'])) {
		$dbResult = (new AdminHelperClass())->adminUpdate([$_POST['actionAdmin'], $_POST['userId']]);
	} else if (!empty($_POST['actionTypeDelete']) && !empty($_POST['threadId'])) {
		$sqlResponse = (new AdminHelperClass())->delete([$_POST['actionTypeDelete'], $_POST['threadId']]);
	} else if (!empty($_POST['actionTypeHide']) && !empty($_POST['threadId'])) {
		$sqlResponse = (new AdminHelperClass())->hide([$_POST['actionTypeHide'], $_POST['threadId']]);
	} else if (!empty($_POST['actionTypeRecover']) && !empty($_POST['threadId'])) {
		$sqlResponse = (new AdminHelperClass())->undelete([$_POST['actionTypeRecover'], $_POST['threadId']]);
	}
}

class AdminHelperClass {
	
	 function loginStatus() : bool {
		if (isset($_SESSION['IS_AUTHORIZED'])) return true;
		return false;
	}

	 function adminStatus() : bool {
		if (!isset($_SESSION['IS_ADMIN']) && !$_SESSION['IS_ADMIN']) return true;
		return false;
	}

	 function undelete(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if ($this->adminStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		(new ThreadsClass())->restore([$inputs[1]]);

		return array("response" => 200);
	}

	 function hide(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if ($this->adminStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		(new ThreadsClass())->hide([$inputs[1]]);

		return array("response" => 200);
	}

	 function delete(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if ($this->adminStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		(new ThreadsClass())->delete([$inputs[1]]);

		return array("response" => 200);
	}

	 function adminUpdate(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if ($this->adminStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		if (!(new UsersClass())->searchUserByID((int)$inputs[1])) return array( "response" => 400, "data" => array("message" => "User Not Found"));
		
		(new AdminClass())->adminUpdate($inputs[0] !== "false" ? 1 : 0, (int) $inputs[1]);

		return array("response" => 200);

	}

	 function updateBlockedUser(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if ($this->adminStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Account is disabled."));
		
		if (!(new UsersClass())->searchUserByID((int)$inputs[1])) return array( "response" => 400, "data" => array("message" => "Not Found"));
		
	
		(new AdminClass())->updateBlockedUser($inputs[0] !== "false" ? 1 : 0, (int) $inputs[1]);

		return array("response" => 200);
	}

	 function viewUserFromUsername(string $inputQuery) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if ($this->adminStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unauthorized attempt. Account is disabled."));
		
		if (strlen($inputQuery) === 0) {
			return (new AdminClass())->getUsers();
		}
		if (!preg_match("/^[a-z0-9]+$/", $inputQuery)) return array( "response" => 400, "data" => array("message" => "Only lowercase letters and numbers are allowed."));
			
		return (new AdminClass())->viewUserFromUsername($inputQuery);
	}

	 function viewThreadFromTitle(string $inputQuery) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		if ($this->adminStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		if (strlen($inputQuery) === 0) {
			return (new AdminClass())->viewThreads();
		}

		if (!preg_match("/^[a-zA-Z0-9\s]+$/", $inputQuery)) return array( "response" => 400, "data" => array("message" => "Title shouldn't contain numbers or special characters."));
		return (new AdminClass())->viewThreadFromTitle($inputQuery);
	}
}

$sqlResponse = json_encode($dbResult, true);
echo $sqlResponse;
?>
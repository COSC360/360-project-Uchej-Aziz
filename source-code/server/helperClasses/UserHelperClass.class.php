<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/UsersClass.class.php';

header('Content-Type: application/json; charset=utf-8');

$sqlResponse = array("response" => 400, "data" => array("message" => "Fields \"Email\" and \"Password\" are empty."));

if ($_SERVER['REQUEST_METHOD'] === "GET") {
	if (!empty($_GET['email']) && !empty($_GET['password'])) {
		$sqlResponse = (new UserHelperClass())->login([$_GET['email'], $_GET['password']]);
	}
} else if ($_SERVER['REQUEST_METHOD'] === "POST") {
	if (!empty($_POST['email']) && !empty($_POST['state'])) {
		$sqlResponse = (new UserHelperClass())->restore([$_POST['email'], 0]);

	}
	else if (!empty($_POST['username']) || !empty($_POST['email']) || (!empty($_POST['password']) && !empty($_POST['repeatpassword']))) {
		if (!empty($_POST['username']) && empty($_POST['email']) && empty($_POST['password']) && empty($_POST['repeatpassword'])) {
			$sqlResponse = (new UserHelperClass())->register(["username" => $_POST['username']]);
		} else if (empty($_POST['username']) && !empty($_POST['email']) && empty($_POST['password']) && empty($_POST['repeatpassword'])) {
			$sqlResponse = (new UserHelperClass())->register(["email" => $_POST['email']]);
		} else if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['repeatpassword'])) {
			$sqlResponse = (new UserHelperClass())->register([$_POST['username'], $_POST['email'], $_POST['password'], $_POST['repeatpassword']]);
		} else {
			$sqlResponse = array("response" => 400, "data" => array("message" => "Invalid information was passed."));
		}
	} else if (!empty($_FILES["img_profile"])) {
		$sqlResponse = (new UserHelperClass())->validateProfileImage($_FILES["img_profile"]);
	} else if (!empty($_POST['aUsername']) && empty($_POST['aOldPassword']) && empty($_POST['aNewPassword'])) {
		$sqlResponse = (new UserHelperClass())->update([$_POST['aUsername']]);
	} else if (empty($_POST['aUsername']) && !empty($_POST['aOldPassword']) && !empty($_POST['aNewPassword'])) {
		$sqlResponse = (new UserHelperClass())->update([$_POST['aOldPassword'], $_POST['aNewPassword']]);
	} else if (!empty($_POST['aUsername']) && !empty($_POST['aOldPassword']) && !empty($_POST['aNewPassword'])) {
		$sqlResponse = (new UserHelperClass())->update([$_POST['aUsername'], $_POST['aOldPassword'], $_POST['aNewPassword']]);
	} else if (!empty($_POST['deleteAccount'])) {
		$sqlResponse = (new UserHelperClass())->delete([]);
	}
}

class UserHelperClass {

	 function loginStatus() : bool {
		if (isset($_SESSION['IS_AUTHORIZED'])) return true;
		return false;
	}

	 function delete(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		return (new UsersClass())->delete([]);
	}

	 function update(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		
		if (count($inputs) === 0) return array("response" => 403);

		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		if (count($inputs) === 1) {
			$username = $inputs[0];
			if (strlen($username) < 3 || strlen($username) > 8)
				return array( "response" => 400, "data" => array("message" => "Username should be between 3 to 8 characters."));

			if (!preg_match("/^[a-z0-9]+$/", $inputs[0]))
				return array( "response" => 400, "data" => array("message" => "Only small letters and numbers are allowed."));
			
			if ((new UsersClass())->findByUsername($inputs[0]))
				return array( "response" => 400, "data" => array("message" => "Username already exists"));

			return (new UsersClass())->update([$username]);
		} else if (count($inputs) == 2) {
			if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/", $inputs[0]))
				return array( "response" => 400, "data" => array("message" => "Password must be minimum 8 characters, one uppercase letter, and one special symbol."));
			
			if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/", $inputs[1]))
				return array( "response" => 400, "data" => array("message" => "Password must be minimum 8 characters, one uppercase letter, and one special symbol."));
			
			return (new UsersClass())->update([$inputs[0], $inputs[1]]);
		} else if (count($inputs) == 3) {
			if (strlen($inputs[0]) < 3 || strlen($inputs[0]) > 8)
				return array( "response" => 400, "data" => array("message" => "Username should be between 3 to 8 characters."));

			if (!preg_match("/^[a-z0-9]+$/", $inputs[0]))
				return array( "response" => 400, "data" => array("message" => "Only small letters and numbers are allowed."));
			
			if ((new UsersClass())->findByUsername($inputs[0])) return array( "response" => 400, "data" => array("message" => "Username already exists"));

			if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/", $inputs[1]))
				return array( "response" => 400, "data" => array("message" => "Password must be minimum 8 characters, one uppercase letter, and one special symbol."));
			
			if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/", $inputs[2]))
				return array( "response" => 400, "data" => array("message" => "Password must be minimum 8 characters, one uppercase letter, and one special symbol."));
			
			return (new UsersClass())->update([$inputs[0], $inputs[1], $inputs[2]]);
		}

		return array("response" => 403);
	}

	 function validateProfileImage(array $inputs) : array {
		if (!$this->loginStatus()) return array("response" => 403);
		
		if (!(new UsersClass())->isActivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Email is not verified."));
		
		if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array( "response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
		
		if ($inputs['size'] == 0 || $inputs['size'] > (5 * 1024 * 1024)) return array("response" => 400, "data" => array("message" => "Image cannot be larger than 5 MB."));
		
		$target_dir  =  $_SERVER["DOCUMENT_ROOT"].'/server/uploads/profilePictures/';
		
		$imageFileType = strtolower(pathinfo($inputs["name"], PATHINFO_EXTENSION));

		$imgFile = "";
		$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

		for($i = 0; $i < 16; $i++)
			$imgFile .= $randString[mt_rand(0, 61)];

		$target_file = $target_dir . basename($imgFile.'.'.strtolower(pathinfo($inputs["name"], PATHINFO_EXTENSION)));

		if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "gif") return array("response" => 400, "data" => array("message" => "Only .jpg, .png, and .gif format accepted."));
         $targetDirThreadBackground = $_SERVER["DOCUMENT_ROOT"].'/server/uploads/profilePictures/';
         // Check if file already exists
         // Added by Aziz
         // uploads folders requires chmod -R 777 to work
         if (!is_dir($targetDirThreadBackground) && !mkdir($targetDirThreadBackground)){
             die("Error creating thread background folder $targetDirThreadBackground");
         }
		if (move_uploaded_file($inputs["tmp_name"], $target_file)) {
			return (new UsersClass())->updateProfileImage($imgFile.'.'.strtolower(pathinfo($inputs["name"], PATHINFO_EXTENSION)));
		} 

		return array("response" => 400, "data" => array("message" => "Invalid information was passed or server error has occured."));
	}

	 function restore(array $inputs) : array {
		if ($this->loginStatus()) return array("response" => 403);

		if (!filter_var($inputs[0], FILTER_VALIDATE_EMAIL)) return array( "response" => 400, "data" => array("message" => "Field \"Email\" is not valid."));

		if (!(new UsersClass())->findByEmail($inputs[0])) return array( "response" => 400, "data" => array("message" => "Email doesn't exist"));
		
		if (!(new UsersClass())->isEmailConfirmed($inputs[0])) return array( "response" => 400, "data" => array("message" => "Email is not confirmed."));

        if(isset($_SESSION['USERNAME'])) {
            if ((new UsersClass())->isDeactivated($_SESSION['USERNAME'])) return array("response" => 400, "data" => array("message" => "Unathorized attempt. Account is disabled."));
        }
		if (!$inputs[1]) {
			return (new TokensClass())->post([$inputs[0], "", "", 0]);
		}

		return array("response" => 400, "data" => array("message" => "Invalid information was passed."));
	}

	 function register(array $inputs) : array {

		if ($this->loginStatus()) return array("response" => 403);
	
		if (count($inputs) == 1 && array_key_exists("username", $inputs)) {
			if (strlen($inputs["username"]) < 3 || strlen($inputs["username"]) > 8)
				return array( "response" => 400, "data" => array("message" => "Username should be between 3 to 8 characters."));

			if (!preg_match("/^[a-z0-9]+$/", $inputs["username"]))
				return array( "response" => 400, "data" => array("message" => "Only small letters and numbers are allowed."));
			
			if ((new UsersClass())->findByUsername($inputs["username"]))
				return array( "response" => 400, "data" => array("message" => "Username already exists"));
			return array("response" => 200);

		} else if (count($inputs) == 1 && array_key_exists("email", $inputs)) {
			if (!filter_var($inputs["email"], FILTER_VALIDATE_EMAIL)) return array( "response" => 400, "data" => array("message" => "Field \"Email\" is not valid."));

			if ((new UsersClass())->findByEmail($inputs["email"]))
				return array( "response" => 400, "data" => array("message" => "Email already exists"));
			return array("response" => 200);

		} else if (count($inputs) == 4) {
			// Extra validation of username
			if (strlen($inputs[0]) < 3 || strlen($inputs[0]) > 8) return array( "response" => 400, "data" => array("message" => "Username should be between 3 to 8 characters."));

			if (!preg_match("/^[a-z0-9]+$/", $inputs[0])) return array( "response" => 400, "data" => array("message" => "Only small letters and numbers are allowed."));
			
			if ((new UsersClass())->findByUsername($inputs[0])) return array( "response" => 400, "data" => array("message" => "Username already exists"));
			
			// Extra validation of email
			if (!filter_var($inputs[1], FILTER_VALIDATE_EMAIL)) return array( "response" => 400, "data" => array("message" => "Field \"Email\" is not valid."));

			if ((new UsersClass())->findByEmail($inputs[1])) return array( "response" => 400, "data" => array("message" => "Email already exists"));
		
			// Validation of passwords

			if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/", $inputs[2]))
				return array( "response" => 400, "data" => array("message" => "Password must be minimum 8 characters, one uppercase letter, and one special symbol."));
			
			if ($inputs[2] != $inputs[3])
				return array( "response" => 400, "data" => array("message" => "11Passwords don't match."));
			return (new UsersClass())->register($inputs);
		}
	}

	 function login(array $inputs) : array {
		if (!filter_var($inputs[0], FILTER_VALIDATE_EMAIL)) return array( "response" => 400, "data" => array("message" => "Field \"Email\" is not valid."));

		if (strlen($inputs[1]) < 6) return array( "response" => 400, "data" => array("message" => "Password should be longer than 5 letters"));

		if ($this->loginStatus()) return array("response" => 403, "data" => array("message" => "User is already logged in."));

		return (new UsersClass())->login($inputs);
	}
}

$sqlResponse = json_encode($sqlResponse, true);
echo $sqlResponse;
?>
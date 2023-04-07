<?php
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/TokensClass.class.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
	header('Content-Type: application/json; charset=utf-8');

	$dbResult = array("response" => 400, "data" => array("message" => "Invalid data."));
	
	if (!empty($_POST['code']) && !empty($_POST['token'])) {
		$dbResult = (new TokenHelperClass())->updateToken([$_POST['code'], $_POST['token'], 1]);
	} else if (!empty($_POST['token']) && !empty($_POST['password']) && !empty($_POST['repeatpassword'])) {
		$dbResult = (new TokenHelperClass())->updateToken([$_POST['password'], $_POST['token'], 0, $_POST['repeatpassword']]);
	}
	$dbResult = json_encode($dbResult, true);
	echo $dbResult;
}

class TokenHelperClass {

	 function loginStatus() : bool {
		if (isset($_SESSION['IS_AUTHORIZED'])) return true;
		return false;
	}

	 function validateToken(array $inputs) : array {
		if (!empty($inputs[0])) {
			if ($this->loginStatus()) return array("response" => 403);
			
			if (preg_match("/^[a-zA-Z0-9]+$/", $inputs[0])) {
				if ($inputs[1] == 1)
					return (new TokensClass())->get([$inputs[0], 1]);
				else if ($inputs[1] == 0)
					return (new TokensClass())->get([$inputs[0], 0]);
			}
			return array("response" => 400, "data" => array("message" => "Invalid token."));
		} else {
			return array("response" => 400, "data" => array("message" => "Token is empty."));
		}
	}
	
	 function updateToken(array $inputs) : array {
		if ($this->loginStatus()) return array("response" => 403);
		
		if (!preg_match("/^[a-zA-Z0-9]+$/", $inputs[1])) return array("response" => 400, "data" => array("message" => "Invalid token."));

		if ($inputs[2] === 1) {
			if (!is_numeric($inputs[0])) return array("response" => 400, "data" => array("message" => "Invalid confirmation code."));

			if ((int) $inputs[0] < 1000 || (int) $inputs[0] > 99999) return array("response" => 400, "data" => array("message" => "Invalid confirmation code."));
			
			if ((new TokensClass())->get([$inputs[1], 1])["response"] !== 200) return array("response" => 400, "data" => array("message" => "Invalid token."));
			
			return (new TokensClass())->update([$inputs[0], $inputs[1], 1]);

		} else if ($inputs[2] === 0) {
			if (!preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[#?!@$%^&*-]).{8,}$/", $inputs[0]))
				return array( "response" => 400, "data" => array("message" => "Password must be minimum 8 characters, one uppercase letter, and one special symbol."));
			
			if ($inputs[0] != $inputs[3])
				return array( "response" => 400, "data" => array("message" => "Passwords don't match."));

			if ((new TokensClass())->get([$inputs[1], 0])["response"] !== 200) return array("response" => 400, "data" => array("message" => "Invalid token."));
			return (new TokensClass())->update([$inputs[0], $inputs[1], 0]);
		}
	}
}
?>
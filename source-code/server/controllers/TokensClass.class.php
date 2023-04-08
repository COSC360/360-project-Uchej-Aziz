<?php 
require_once $_SERVER["DOCUMENT_ROOT"].'/server/settings/ConnectDB.class.php';

class TokensClass {

	 function get(array $inputs) : array {
		$dbCon = (new ConnectDB())->connect();
		
		if ($inputs[1] === 1) {
			$sqlQuery = "SELECT tblUsers.isUserConfirmed, endDate FROM tblTokens JOIN tblUsers ON tblTokens.idUser = tblUsers.id WHERE isUserConfirmed = 1 AND code='$inputs[0]'";
			$sqlResult = mysqli_query($dbCon, $sqlQuery);
			$row = mysqli_fetch_row($sqlResult);	 
			
			if (is_null($row)) {
				mysqli_close($dbCon);
				return array("response" => 400, "data" => array("message" => "Invalid code."));
			}
			
			if ((int) $row[0] === 1) {
				mysqli_close($dbCon);
				return array("response" => 400, "data" => array("message" => "Email has been confirmed previously."));
			}
			
			if (strtotime($row[1]) < time()) {
				mysqli_close($dbCon);
				return array("response" => 400, "data" => array("message" => "code expired."));
			}
		} else if ($inputs[1] === 0) {
			$sqlQuery = "SELECT endDate FROM tblTokens WHERE isUserConfirmed = 0 AND code='$inputs[0]'";
			$sqlResult = mysqli_query($dbCon, $sqlQuery);
			$row = mysqli_fetch_row($sqlResult);	 

			if (is_null($row)) {
				mysqli_close($dbCon);
				return array("response" => 400, "data" => array("message" => "Invalid code."));
			}

			if (strtotime($row[0]) < time()) {
				mysqli_close($dbCon);
				return array("response" => 400, "data" => array("message" => "code expired."));
			}
		}

		mysqli_close($dbCon);
		return array("response" => 200);
	}



	 function post(array $inputs) : array {
		$dbCon = (new ConnectDB())->connect();
        $sqlQuery = "";
		if ($inputs[3] === 1) {
			$code1 = rand(1000, 99999);
			$sqlQuery = "INSERT INTO tblTokens(code, key, endDate, idUser) VALUES ('$inputs[0]', $code1, '".date('Y-m-d H:i:s', $inputs[1])."', $inputs[2])";
            $result = mysqli_query($dbCon, $sqlQuery);
        } else if ($inputs[3] === 0) {
			$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		
			$end_token = time() + 86400;
			$token = '';

			for ($i = 0; $i < 60; $i++)
				$token .= $randString[mt_rand(0, 61)];
			
			$sqlQuery = "SELECT id FROM tblUsers WHERE email='$inputs[0]' LIMIT 1";
			$sqlResult = mysqli_query($dbCon, $sqlQuery);
			
			$row = mysqli_fetch_row($sqlResult);
			$sqlQuery = "INSERT INTO tblTokens(code, endDate, idUser, isUserConfirmed) VALUES ('$token', '".date('Y-m-d H:i:s', $end_token)."', $row[0], 0)";
            $result = mysqli_query($dbCon, $sqlQuery);
		}


		return array("response" => 200);
	}

	 function update(array $inputs) : array {
		$dbCon = (new ConnectDB())->connect();
		if ($inputs[2] === 1) {
			$sqlQuery = "SELECT key, idUser FROM tblTokens WHERE code='$inputs[1]'";
			$sqlResult = mysqli_query($dbCon, $sqlQuery);
			$row = mysqli_fetch_row($sqlResult);	 

			if ($row[0] !== $inputs[0]) {
				mysqli_close($dbCon);
				return array("response" => 400, "data" => array("message" => "Invalid confirmation code."));
			}

			$sqlQuery = "UPDATE tblUsers SET isUserConfirmed = 1 WHERE id=$row[1]";
			mysqli_query($dbCon, $sqlQuery);
		} else if ($inputs[2] === 0) {

			$sqlQuery = "SELECT idUser FROM tblTokens WHERE code='$inputs[1]'";
			$sqlResult = mysqli_query($dbCon, $sqlQuery);
			$row = mysqli_fetch_row($sqlResult);	 

			$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			$passHash = '';
			
			for($i = 0; $i < 10; $i++)
				$passHash .= $randString[mt_rand(0, 61)];
					
			$password = hash('sha256', $inputs[0] . $passHash);

			$sqlQuery = "UPDATE tblUsers SET password = '$password', salt = '$passHash' WHERE id = $row[0]";
			mysqli_query($dbCon, $sqlQuery);

			$sqlQuery = "UPDATE tblTokens SET endDate = '".date('Y-m-d H:i:s', time())."' WHERE code = '$inputs[1]'";
			mysqli_query($dbCon, $sqlQuery);
		}


		mysqli_close($dbCon);
		return array("response" => 200);
	}


}
?>
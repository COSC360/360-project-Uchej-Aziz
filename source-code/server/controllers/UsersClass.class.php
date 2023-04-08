<?php 
@session_start();

require_once $_SERVER["DOCUMENT_ROOT"].'/server/controllers/TokensClass.class.php';

class UsersClass {
		

	 function debug_to_console($data) {
		$output = $data;
		if (is_array($output))
			$output = implode(',', $output);
	
		echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
	}


	 function register(array $inputs) : array {
		
		
		$dbCon = (new ConnectDB())->connect();

		$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
		
		$passHash = '';
		
		for($i = 0; $i < 10; $i++)
			$passHash .= $randString[mt_rand(0, 61)];
        
		$password = hash('sha256', $inputs[2] . $passHash);
		
		$sqlQuery = "INSERT INTO tblUsers(username, email, password, salt) VALUES ('$inputs[0]', '$inputs[1]', '$password', '$passHash')";
		mysqli_query($dbCon, $sqlQuery);
	
		$sqlQuery = "SELECT id FROM tblUsers WHERE username='$inputs[0]' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$row = mysqli_fetch_row($sqlResult);
		
		$end_token = time() + 86400;
		$token = '';

		for ($i = 0; $i < 60; $i++)
			$token .= $randString[mt_rand(0, 61)];

		$token = (new TokensClass())->post([$token, $end_token, $row[0], 1]);
		
		mysqli_close($dbCon);
		return array("response" => 200);
	}
		

	 function login(array $inputs) : array {
		$sqlResult = array("response" => 400, "data" => array("message" => "User doesn't exist."));
		
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT username, email, password, isUserConfirmed, salt, status, profile_image, adminStatus FROM tblUsers WHERE email='$inputs[0]'";
		$dbResult = mysqli_query($dbCon, $sqlQuery);
		if($dbResult){
		while($row = mysqli_fetch_assoc($dbResult)) {
			if (!$row['isUserConfirmed']) {
				$sqlResult["data"] = array("message" => "Email is not verified");
				break;
			}

			if ($row['status']) {
				$sqlResult["data"] = array("message" => "Account is disabled by Administrator.");
				break;
			}

			$passChecker = hash('sha256', $inputs[1] . $row['salt']);

			if ($row['password'] === $passChecker) {
				$sqlResult =array("response" => 200);
				$_SESSION['IS_AUTHORIZED'] = true;
				$_SESSION['USERNAME'] = $row['username'];
				$_SESSION['USER_IMAGE'] = 'http://'.$_SERVER['HTTP_HOST'].'/server/uploads/profilePictures/'.$row['profile_image'];
				$_SESSION['IS_ADMIN'] = $row['adminStatus'];
				break;
			} else {
				$sqlResult["response"] = 400;
				$sqlResult["data"] = array("message" => "Password is not correct.");
				break;
			}
		}
	}
		mysqli_close($dbCon);
		return $sqlResult;
	}

	 function updateProfileImage($file) : array {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "UPDATE tblUsers SET profile_image = '$file' WHERE username='".$_SESSION['USERNAME']."'";

		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		$_SESSION['USER_IMAGE'] = 'http://'.$_SERVER['HTTP_HOST'].'/server/uploads/profilePictures/'.$file;
		mysqli_close($dbCon);
		return array("response" => 200);
	}

//

	 function update(array $inputs) : array {
		if (count($inputs) === 1) {
			$dbCon = (new ConnectDB())->connect();
			$sqlQuery = "UPDATE tblUsers SET username = '$inputs[0]' WHERE username='".$_SESSION['USERNAME']."'";
			$sqlResult = mysqli_query($dbCon, $sqlQuery);
			
			mysqli_close($dbCon);
			$_SESSION['USERNAME'] = $inputs[0];
			return array("response" => 200);
		} else if (count($inputs) === 2) {
			$dbCon = (new ConnectDB())->connect();

			$sqlQuery = "SELECT password, salt FROM tblUsers WHERE username='".$_SESSION['USERNAME']."' LIMIT 1";
			$sqlResult = mysqli_query($dbCon, $sqlQuery);
			
			$row = mysqli_fetch_row($sqlResult);

			$passChecker = hash('sha256', $inputs[0] . $row[1]);

			if ($passChecker != $row[0]) {
				mysqli_close($dbCon);
				return array("response" => 400, "data" => array("message" => "Old password is incorrect."));
			}

			$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			$passHash = '';
			
			for($i = 0; $i < 10; $i++)
				$passHash .= $randString[mt_rand(0, 61)];
					
			$password = hash('sha256', $inputs[1] . $passHash);

			$sqlQuery = "UPDATE tblUsers SET password = '$password', salt = '$passHash' WHERE username='".$_SESSION['USERNAME']."'";
			mysqli_query($dbCon, $sqlQuery);

			mysqli_close($dbCon);
			return array("response" => 200);
		} else if (count($inputs) === 3) {
			$dbCon = (new ConnectDB())->connect();

			$sqlQuery = "SELECT password, salt FROM tblUsers WHERE username='".$_SESSION['USERNAME']."' LIMIT 1";
			$sqlResult = mysqli_query($dbCon, $sqlQuery);
			
			$row = mysqli_fetch_row($sqlResult);

			$passChecker = hash('sha256', $inputs[1] . $row[1]);

			if ($passChecker != $row[0]) {
				mysqli_close($dbCon);
				return array("response" => 400, "data" => array("message" => "Old password is incorrect."));
			}

			$randString = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			$passHash = '';
			
			for($i = 0; $i < 10; $i++)
				$passHash .= $randString[mt_rand(0, 61)];
					
			$password = hash('sha256', $inputs[2] . $passHash);

			$sqlQuery = "UPDATE tblUsers SET password = '$password', salt = '$passHash', username='$inputs[0]' WHERE username='".$_SESSION['USERNAME']."'";
			mysqli_query($dbCon, $sqlQuery);

			mysqli_close($dbCon);
			$_SESSION['USERNAME'] = $inputs[0];
			return array("response" => 200);
		}
		return array();
	}

	 function delete(array $inputs) : array {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "UPDATE tblUsers SET status = 1 WHERE username='".$_SESSION['USERNAME']."'";
		mysqli_query($dbCon, $sqlQuery);

		mysqli_close($dbCon);
		return array("response" => 200);
	}



	 function searchUserByID(int $id) : bool {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT id FROM tblUsers WHERE id='$id'";
		$dbResult = mysqli_query($dbCon, $sqlQuery);

		while($row = mysqli_fetch_assoc($dbResult)) {
			mysqli_close($dbCon);
			return true;
		}
		mysqli_close($dbCon);
		return false;
	}

	 function isDeactivated(string $username) : bool {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT status FROM tblUsers WHERE username='$username' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$row = mysqli_fetch_row($sqlResult);
		
		mysqli_close($dbCon);
		return $row[0];
	}

	 function findByEmail(string $email) : bool {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT email FROM tblUsers WHERE email='$email'";
		$dbResult = mysqli_query($dbCon, $sqlQuery);
		if($dbResult){
		while($row = mysqli_fetch_assoc($dbResult)) {
			mysqli_close($dbCon);
			return true;
		}
	}
		mysqli_close($dbCon);
		return false;
	}

	 function isActivated(string $username) : bool {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT isUserConfirmed FROM tblUsers WHERE username='$username' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$row = mysqli_fetch_row($sqlResult);

		mysqli_close($dbCon);
		return $row[0];
	}

	 function isEmailConfirmed(string $email) : bool {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT isUserConfirmed FROM tblUsers WHERE email='$email' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$row = mysqli_fetch_row($sqlResult);

		mysqli_close($dbCon);
		return $row[0];
	}

	 function findByUsername(string $username) : bool {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT username FROM tblUsers WHERE username='$username'";
		$dbResult = mysqli_query($dbCon, $sqlQuery);
		if($dbResult){
		while($row = mysqli_fetch_assoc($dbResult)) {
			mysqli_close($dbCon);
			return true;
		}
	}
		mysqli_close($dbCon);
		return false;
	}


	 function findPostsAndComments(array $inputs) : array {
		
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "";
		if ($inputs[1] == 0) {
			$sqlQuery = "(SELECT content, tblThreads.link, 'tblComments' as body_comments FROM tblComments JOIN tblUsers ON tblUsers.id = tblComments.idUser JOIN tblThreads ON tblThreads.idThread = tblComments.idThread WHERE tblUsers.username = '$inputs[0]' ORDER BY tblComments.timestampCreated) UNION (SELECT content, tblThreads.link, 'tblPosts' as body_posts FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread WHERE tblUsers.username = '$inputs[0]' ORDER BY tblPosts.timestampCreated)";
		} else if ($inputs[1] == 1) {
			$sqlQuery = "(SELECT content, tblThreads.link, 'tblComments' as body_comments FROM tblComments JOIN tblUsers ON tblUsers.id = tblComments.idUser JOIN tblThreads ON tblThreads.idThread = tblComments.idThread WHERE tblUsers.id = '$inputs[0]' ORDER BY tblComments.timestampCreated) UNION (SELECT content, tblThreads.link, 'tblPosts' as body_posts FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread WHERE tblUsers.id = '$inputs[0]' ORDER BY tblPosts.timestampCreated)";
		}
		$dbResult = mysqli_query($dbCon, $sqlQuery);

		$sqlResult = array();

		while($row = mysqli_fetch_assoc($dbResult)) {
			$sqlResult[] = array(
                "content" => $row['content'],
                "url" => $row['link'],
                "type" => $row['body_comments']);
		}
		mysqli_close($dbCon);
		return $sqlResult;
	}
}
?>
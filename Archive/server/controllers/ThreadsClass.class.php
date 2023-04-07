<?php 
@session_start();
require_once $_SERVER["DOCUMENT_ROOT"].'/server/controllers/UsersClass.class.php';
require_once $_SERVER["DOCUMENT_ROOT"].'/server/settings/ConnectDB.class.php';

class ThreadsClass {
   function get(array $inputs) : array {
		return array();
	}

	 function getTopThreads() : array {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "SELECT tblThreads.link, COUNT(tblPosts.idPost) as top FROM `tblThreads` LEFT JOIN tblPosts ON tblThreads.idThread = tblPosts.idThread GROUP BY tblThreads.idThread ORDER BY top DESC LIMIT 5";
		$dbResult = mysqli_query($dbCon, $sqlQuery);

		$sqlResult = array();
		if($dbResult){
		while($row = mysqli_fetch_assoc($dbResult)) {
			$sqlResult[] = [

                "link" => $row['link'],
                "total_posts" => $row['top']
            ];
		}
	}
		mysqli_close($dbCon);
		return $sqlResult;
	}

	 function viewThreads() : array {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "SELECT tblThreads.title, tblThreads.link, tblThreads.bgImage, tblThreads.image FROM tblThreads";

		$dbResult = mysqli_query($dbCon, $sqlQuery);

		$sqlResult = array();
		if($dbResult){
		while($row = mysqli_fetch_assoc($dbResult)) {
			$sqlResult[] = [
                "title" => $row['title'],
                "link" => $row['link'],
                "thread_background_picture" => $row['bgImage'],
                "thread_cover_picture" => $row['image']
            ];
		}
	}
		mysqli_close($dbCon);
		return $sqlResult;
	}

	 function getThreadByQuery(string $inputQuery) : array {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "SELECT tblThreads.title, tblThreads.link, tblThreads.bgImage, tblThreads.image FROM tblThreads WHERE title LIKE '%$inputQuery%' OR link LIKE '%$inputQuery%'";
		$dbResult = mysqli_query($dbCon, $sqlQuery);

		$sqlResult = array();

		while($row = mysqli_fetch_assoc($dbResult)) {
			$sqlResult[] = [
                "title" => $row['title'],
                "link" => $row['link'],
                "thread_background_picture" => $row['bgImage'],
                "thread_cover_picture" => $row['image']
            ];
		}
		mysqli_close($dbCon);
		return $sqlResult;
	}


	 function post(array $inputs) : array {
		$sqlResult = array("response" => 400, "data" => array("message" => "Cannot create thread."));
        $user_id = "";
		$dbCon = (new ConnectDB())->connect();

		$get_user_query = "SELECT id FROM tblUsers WHERE username = '".$_SESSION["USERNAME"]."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $get_user_query);

		while ($row = mysqli_fetch_assoc($sqlResult)) {
			$user_id = $row["id"];
		}

		$sqlQuery = "INSERT INTO tblThreads(title, link, bgImage, image, idUser)
				VALUES ('$inputs[0]', '$inputs[1]', '$inputs[2]', '$inputs[3]', $user_id)";
		mysqli_query($dbCon, $sqlQuery);
		mysqli_close($dbCon);
		return array("response" => 200);
	}

	 function findThreadByUrl(string $url) : bool {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "SELECT link FROM tblThreads where link = '$url' AND isRowDeleted = 0";
		
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		while ($row = mysqli_fetch_assoc($sqlResult)) {
			mysqli_close($dbCon);
			return true;
		}
		mysqli_close($dbCon);
		return false;
	}

	 function update(array $inputs) : array {
		return array();
	}

	 function delete(array $inputs) : array {

		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "UPDATE tblThreads SET isRowDeleted = 1 WHERE idThread = ".$inputs[0];
		mysqli_query($dbCon, $sqlQuery);

		$sqlQuery = "SELECT id FROM tblUsers WHERE username = '".$_SESSION['USERNAME']."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$is_admin = mysqli_fetch_row($sqlResult);

		$sqlQuery = "SELECT idUser FROM tblThreads WHERE idThread = ".$inputs[0]." LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$row = mysqli_fetch_row($sqlResult);

		$sqlQuery = "INSERT INTO tblNotifications(idUser, idUserReply, notificationType, idThread) VALUES (".$row[0].", ".$is_admin[0].", 6, ".$inputs[0].")";
		mysqli_query($dbCon, $sqlQuery);
		mysqli_close($dbCon);
		return array("response" => 200);
	}

	 function hide(array $inputs) : array {

		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "UPDATE tblThreads SET isRowHidden = 1 WHERE idThread = ".$inputs[0];
		mysqli_query($dbCon, $sqlQuery);

		$sqlQuery = "SELECT id FROM tblUsers WHERE username = '".$_SESSION['USERNAME']."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$is_admin = mysqli_fetch_row($sqlResult);

		$sqlQuery = "SELECT idUser FROM tblThreads WHERE idThread = ".$inputs[0]." LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		
		$row = mysqli_fetch_row($sqlResult);

		$sqlQuery = "INSERT INTO tblNotifications(idUser, idUserReply, notificationType, idThread) VALUES (".$row[0].", ".$is_admin[0].", 6, ".$inputs[0].")";
		mysqli_query($dbCon, $sqlQuery);
		mysqli_close($dbCon);
		return array("response" => 200);
	}

	 function restore(array $inputs) : array {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "UPDATE tblThreads SET isRowHidden = 0, isRowDeleted = 0 WHERE idThread = ".$inputs[0];
		mysqli_query($dbCon, $sqlQuery);

		mysqli_close($dbCon);
		return array("response" => 200);
	}


	 function getTitle(string $inputs): string {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT title FROM tblThreads WHERE link = '$inputs' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		$title = mysqli_fetch_row($sqlResult);
		return $title[0];
	}

	 function getThread(string $threadUrl): array
     {
		$dbCon = (new ConnectDB())->connect();

		$sqlQuery = "SELECT tblThreads.title, tblThreads.bgImage, tblThreads.image, tblThreads.isRowHidden, CASE WHEN EXISTS(SELECT tblUserThreads.idUser FROM tblUserThreads JOIN tblUsers ON tblUserThreads.idUser = tblUsers.id WHERE tblUsers.username = '".$_SESSION["USERNAME"]."' AND tblUserThreads.idThread = tblThreads.idThread) THEN 1 ELSE 0 END as isSubscribed FROM tblThreads WHERE tblThreads.link = '$threadUrl'";
		$dbResult = mysqli_query($dbCon, $sqlQuery);

		$sqlResult = array();

		while($row = mysqli_fetch_assoc($dbResult)) {
			$sqlResult[] = [
                "title" => $row['title'],
                "thread_background" => $row['bgImage'],
                "thread_profile" => $row['image'],
                "isRowHidden" => $row['isRowHidden'],
                "isSubscribed" => $row['isSubscribed']
            ];
		}
		mysqli_close($dbCon);
		return $sqlResult;
	}

	 function userThreadsOperations(array $inputs): array {
		$dbCon = (new ConnectDB())->connect();
		$dataStatus = (int)$inputs[1];
        $idThread = '';
        $user_id = '';
		$get_user_query = "SELECT id FROM tblUsers WHERE username = '".$_SESSION["USERNAME"]."' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $get_user_query);
		while ($row = mysqli_fetch_assoc($sqlResult)) {
			$user_id = $row["id"];
		}
		
		$sqlQuery = "SELECT idThread FROM tblThreads WHERE link = '$inputs[0]' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		while ($row = mysqli_fetch_assoc($sqlResult)) {
			$idThread = $row["idThread"];
		}
		
		switch ($dataStatus) {
			case 0:
				$sqlQuery = "INSERT INTO tblUserThreads(idThread, idUser) VALUES ($idThread, $user_id)";
				break;
			case 1:
				$sqlQuery = "DELETE FROM tblUserThreads WHERE idThread=$idThread AND idUser=$user_id";
				break;
		}

		mysqli_query($dbCon, $sqlQuery);
		mysqli_close($dbCon);
		return array("response" => 200);
	}

	 function getTopUsers(string $url): array {
		$dbCon = (new ConnectDB())->connect();
		$sqlQuery = "SELECT tblThreads.idThread FROM tblThreads WHERE tblThreads.link = '$url' LIMIT 1";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
        $idThread = '';
		while ($row = mysqli_fetch_assoc($sqlResult)) {
			$idThread = $row["idThread"];
		}
		
		$sqlQuery = "SELECT count(tblPosts.idUser), tblUsers.id, tblUsers.username, tblUsers.profile_image FROM tblPosts JOIN tblUsers ON tblPosts.idUser=tblUsers.id WHERE tblPosts.idThread=$idThread GROUP BY (tblPosts.idUser) ORDER BY count(tblPosts.idUser) DESC LIMIT 5";
		$sqlResult = mysqli_query($dbCon, $sqlQuery);
		$sqlResultArray = array();
		while ($row = mysqli_fetch_assoc($sqlResult)) {
			$sqlResultArray[] = [
                "count" => $row["count(tblPosts.idUser)"],
                "username" => $row["username"],
                "profile_image" => $row["profile_image"],
                "userId" => $row["id"]
            ];
		}
		return $sqlResultArray;
	}
}
?>
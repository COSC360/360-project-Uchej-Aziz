<?php
	@session_start();
	require_once $_SERVER["DOCUMENT_ROOT"].'/server/settings/ConnectDB.class.php';


	class AdminClass {



		 function viewThreads() : array {
			$dbCon = (new ConnectDB())->connect();
			$sqlQuery = "SELECT tblThreads.idThread, tblThreads.title, tblThreads.link, DATE_FORMAT((tblThreads.timestampCreated), '%M %D, %Y') as created_date, tblUsers.id as ownerId, tblUsers.username as ownerName, tblThreads.isRowHidden, tblThreads.isRowDeleted, COUNT(tblUserThreads.idUser) as members FROM `tblThreads` JOIN tblUsers ON tblThreads.idUser = tblUsers.id LEFT JOIN tblUserThreads ON tblUserThreads.idThread = tblThreads.idThread GROUP BY tblThreads.idThread ORDER BY tblThreads.idThread";

			$sqlResponse = mysqli_query($dbCon, $sqlQuery);
			$sqlResult = array();

			while($row = mysqli_fetch_assoc($sqlResponse)) {
				$sqlResult[] = [
                    "idThread" => $row['idThread'],
                    "title" => $row['title'],
                    "link" => $row['link'],
                    "created_date" => $row['created_date'],
                    "ownerId" => $row['ownerId'],
                    "ownerName" => $row['ownerName'],
                    "isRowHidden" => $row['isRowHidden'],
                    "isRowDeleted" => $row['isRowDeleted'],
                    "members" => $row['members']
                ];
			}
			mysqli_close($dbCon);
			return $sqlResult;
		}

         function getAllStatistics() : array {
            $dbCon = (new ConnectDB())->connect();
            $sqlQuery = "SELECT 'tblUsers' AS table_name, COUNT(*) as result FROM tblUsers UNION SELECT 'tblThreads' AS table_name, COUNT(*) as result FROM tblThreads UNION SELECT 'tblComments' AS table_name, COUNT(*) as result FROM tblComments UNION SELECT 'tblPosts' AS table_name, COUNT(*) as result FROM tblPosts";
            $sqlResponse = mysqli_query($dbCon, $sqlQuery);
            $sqlResult = array();
            while($row = mysqli_fetch_assoc($sqlResponse)) {
                $sqlResult[] = $row['result'];
            }

            mysqli_close($dbCon);
            return $sqlResult;
        }


		 function updateBlockedUser(bool $input, int $userId) : void {
			$dbCon = (new ConnectDB())->connect();

			if ((int)$input === 1) {
				$sqlQuery = "UPDATE tblUsers SET status = 1 WHERE id = $userId";
				mysqli_query($dbCon, $sqlQuery);
			} else if ((int)$input === 0){
				$sqlQuery = "UPDATE tblUsers SET status = 0 WHERE id = $userId";
				mysqli_query($dbCon, $sqlQuery);
			}
			mysqli_close($dbCon);
		}

         function getUsers() : array {
            $dbCon = (new ConnectDB())->connect();
            $sqlQuery = "SELECT id, username, DATE_FORMAT((timestampCreated), '%M %D, %Y') as regdate, email, isUserConfirmed, adminStatus, status FROM tblUsers";

            $sqlResponse = mysqli_query($dbCon, $sqlQuery);
            $sqlResult = array();

            while($row = mysqli_fetch_assoc($sqlResponse)) {
                $sqlResult[] = [
                    "id" => $row['id'],
                    "username" => $row['username'],
                    "regdate" => $row['regdate'],
                    "email" => $row['email'],
                    "isUserConfirmed" => $row['isUserConfirmed'],
                    "adminStatus" => $row['adminStatus'],
                    "status" => $row['status']
                ];
            }
            mysqli_close($dbCon);
            return $sqlResult;
        }

		 function adminUpdate(bool $input, int $userId) : void {
			$dbCon = (new ConnectDB())->connect();

			if ((int)$input === 1) {
				$sqlQuery = "UPDATE tblUsers SET adminStatus = 1 WHERE id = $userId";
				mysqli_query($dbCon, $sqlQuery);
			} else if ((int)$input === 0){
				$sqlQuery = "UPDATE tblUsers SET adminStatus = 0 WHERE id = $userId";
				mysqli_query($dbCon, $sqlQuery);
			}
			mysqli_close($dbCon);
		}



		 function viewUserFromUsername(string $inputQuery) : array {
			$dbCon = (new ConnectDB())->connect();
			$sqlQuery = "SELECT id, username, DATE_FORMAT((timestampCreated), '%M %D, %Y') as regdate, email, isUserConfirmed, adminStatus, status FROM tblUsers WHERE username LIKE '%".$inputQuery."%'";

			$sqlResponse = mysqli_query($dbCon, $sqlQuery);
			$sqlResult = array();
			$found = false;

			while($row = mysqli_fetch_assoc($sqlResponse)) {
				$sqlResult[] = [
                    "id" => $row['id'],
                    "username" => $row['username'],
                    "regdate" => $row['regdate'],
                    "email" => $row['email'],
                    "isUserConfirmed" => $row['isUserConfirmed'],
                    "adminStatus" => $row['adminStatus'],
                    "status" => $row['status']
                ];
				$found = true;
			}
			mysqli_close($dbCon);
			if (!$found) return array("response" => 400);
			return ["response" => 200, "data" => [$sqlResult]];
		}

         function viewThreadFromTitle(string $inputQuery) : array {
            $dbCon = (new ConnectDB())->connect();
            $sqlQuery = "SELECT tblThreads.idThread, tblThreads.title, tblThreads.link, DATE_FORMAT((tblThreads.timestampCreated), '%M %D, %Y') as created_date, tblUsers.id as ownerId, tblUsers.username as ownerName, tblThreads.isRowHidden, tblThreads.isRowDeleted, COUNT(tblUserThreads.idUser) as members FROM `tblThreads` JOIN tblUsers ON tblThreads.idUser = tblUsers.id JOIN tblUserThreads ON tblUserThreads.idThread = tblThreads.idThread WHERE tblThreads.title LIKE '%$inputQuery%' GROUP BY tblThreads.idThread ORDER BY tblThreads.idThread";

            $sqlResponse = mysqli_query($dbCon, $sqlQuery);
            $sqlResult = array();
            $found = false;

            while($row = mysqli_fetch_assoc($sqlResponse)) {
                $sqlResult[] = [
                    "idThread" => $row['idThread'],
                    "title" => $row['title'],
                    "link" => $row['link'],
                    "created_date" => $row['created_date'],
                    "ownerId" => $row['ownerId'],
                    "ownerName" => $row['ownerName'],
                    "isRowHidden" => $row['isRowHidden'],
                    "isRowDeleted" => $row['isRowDeleted'],
                    "members" => $row['members']
                ];
                $found = true;
            }
            mysqli_close($dbCon);
            if (!$found) return array("response" => 400);
            return ["response" => 200, "data" => [$sqlResult]];
        }

	}
?>
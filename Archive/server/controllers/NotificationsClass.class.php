<?php 
	@session_start();
	require_once $_SERVER["DOCUMENT_ROOT"].'/server/settings/ConnectDB.class.php';
	class NotificationsClass {
		 function get(array $inputs) : array {
			$dbCon = (new ConnectDB())->connect();

			$sqlQuery = "SELECT tblNotifications.idUserReply, tblNotificationEnum.content, tblNotifications.notificationType, DATE_FORMAT(tblNotifications.timestampCreated, '%Y-%m-%d') as date_created, u2.username as replied_username, tblThreads.link  FROM `tblNotifications` JOIN `tblNotificationEnum` ON tblNotifications.notificationType = tblNotificationEnum.id JOIN tblUsers ON tblNotifications.idUser = tblUsers.id JOIN tblUsers as u2 ON tblNotifications.idUserReply = u2.id JOIN tblThreads ON tblNotifications.idThread = tblThreads.idThread WHERE tblUsers.username = '".$_SESSION["USERNAME"]."' ORDER BY tblNotifications.timestampCreated DESC";
			$dbResult = mysqli_query($dbCon, $sqlQuery);

			$notifications = array();
			while($row = mysqli_fetch_assoc($dbResult)) {
				
				if (date("Y-m-d", strtotime('today')) == $row["date_created"]) {
					if (!array_key_exists("Today", $notifications)) {
						$notifications["Today"][0] = [
							"idUserReply" => $row["idUserReply"],
							"content" => $row["content"],
							"notificationType" => $row["notificationType"],
							"replied_username" => $row["replied_username"],
							"link" => $row["link"]
						];
					} else {
						$notifications["Today"][] = [
                            "idUserReply" => $row["idUserReply"],
                            "content" => $row["content"],
                            "notificationType" => $row["notificationType"],
                            "replied_username" => $row["replied_username"],
                            "link" => $row["link"]
                        ];
					}
				} else if (date("Y-m-d", strtotime('yesterday')) == $row["date_created"]) {
					if (!array_key_exists("Yesterday", $notifications)) {
						$notifications["Yesterday"][0] = [
							"idUserReply" => $row["idUserReply"],
							"content" => $row["content"],
							"notificationType" => $row["notificationType"],
							"replied_username" => $row["replied_username"],
							"link" => $row["link"]
						];
					} else {
						$notifications["Yesterday"][] = [
                            "idUserReply" => $row["idUserReply"],
                            "content" => $row["content"],
                            "notificationType" => $row["notificationType"],
                            "replied_username" => $row["replied_username"],
                            "link" => $row["link"]
                        ];
					}
				} else if (strtotime("this week") <= strtotime($row["date_created"])) {
					if (!array_key_exists("This Week", $notifications)) {
						$notifications["This Week"][0] = [
							"idUserReply" => $row["idUserReply"],
							"content" => $row["content"],
							"notificationType" => $row["notificationType"],
							"replied_username" => $row["replied_username"],
							"link" => $row["link"]
						];
					} else {
						$notifications["This Week"][] = [
                            "idUserReply" => $row["idUserReply"],
                            "content" => $row["content"],
                            "notificationType" => $row["notificationType"],
                            "replied_username" => $row["replied_username"],
                            "link" => $row["link"]
                        ];
					}
				} else {
					if (!array_key_exists("Long Time Ago", $notifications)) {
						$notifications["Long Time Ago"][0] = [
							"idUserReply" => $row["idUserReply"],
							"content" => $row["content"],
							"notificationType" => $row["notificationType"],
							"replied_username" => $row["replied_username"],
							"link" => $row["link"]
						];
					} else {
						$notifications["Long Time Ago"][] = [
                            "idUserReply" => $row["idUserReply"],
                            "content" => $row["content"],
                            "notificationType" => $row["notificationType"],
                            "replied_username" => $row["replied_username"],
                            "link" => $row["link"]
                        ];
					}
				}
			}
			mysqli_close($dbCon);
			return $notifications;
		}

	}
?>
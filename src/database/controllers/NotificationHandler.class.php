<?php 
	@session_start();
	require_once $_SERVER["DOCUMENT_ROOT"].'/server/helpers/Controller.class.php';
	require_once $_SERVER["DOCUMENT_ROOT"].'/server/services/DatabaseConnector.class.php';


	class NotificationHandler extends Controller {
		public function fetch(array $parameters) : array {
			$dbConnection = (new DatabaseConnector())->getConnection();

			$sqlQuery = "SELECT UserNotifications.replied_user_id, NotificationTypes.notification_type_description, UserNotifications.type_action, DATE_FORMAT(UserNotifications.timestamp_submitted, '%Y-%m-%d') as date_created, u2.username as replied_user_id, forum_threads.threadUrl  FROM `UserNotifications` JOIN `NotificationTypes` ON UserNotifications.type_action = NotificationTypes.notification_type_id JOIN users ON UserNotifications.userId = users.userId JOIN users as u2 ON UserNotifications.replied_user_id = u2.userId JOIN forum_threads ON UserNotifications.related_thread_id = forum_threads.related_thread_id WHERE users.username = '".$_SESSION["USERNAME"]."' ORDER BY UserNotifications.timestamp_submitted DESC";
			$resp = mysqli_query($dbConnection, $sqlQuery);

			$UserNotifications = array();
			while($tableData = mysqli_fetch_assoc($resp)) {
				
				if (date("Y-m-d", strtotime('today')) == $tableData["date_created"]) {
					if (!array_key_exists("Today", $UserNotifications)) {
						$UserNotifications["Today"][0] = [
							"replied_user_id" => $tableData["replied_user_id"],
							"notification_type_description" => $tableData["notification_type_description"],
							"type_action" => $tableData["type_action"],
							"replied_user_id" => $tableData["replied_user_id"],
							"threadUrl" => $tableData["threadUrl"]
						];
					} else {
						array_push($UserNotifications["Today"], [
							"replied_user_id" => $tableData["replied_user_id"],
							"notification_type_description" => $tableData["notification_type_description"],
							"type_action" => $tableData["type_action"],
							"replied_user_id" => $tableData["replied_user_id"],
							"threadUrl" => $tableData["threadUrl"]
						]);
					}
				} else if (date("Y-m-d", strtotime('yesterday')) == $tableData["date_created"]) {
					if (!array_key_exists("Yesterday", $UserNotifications)) {
						$UserNotifications["Yesterday"][0] = [
							"replied_user_id" => $tableData["replied_user_id"],
							"notification_type_description" => $tableData["notification_type_description"],
							"type_action" => $tableData["type_action"],
							"replied_user_id" => $tableData["replied_user_id"],
							"threadUrl" => $tableData["threadUrl"]
						];
					} else {
						array_push($UserNotifications["Yesterday"], [
							"replied_user_id" => $tableData["replied_user_id"],
							"notification_type_description" => $tableData["notification_type_description"],
							"type_action" => $tableData["type_action"],
							"replied_user_id" => $tableData["replied_user_id"],
							"threadUrl" => $tableData["threadUrl"]
						]);
					}
				} else if (strtotime("this week") <= strtotime($tableData["date_created"])) {
					if (!array_key_exists("This Week", $UserNotifications)) {
						$UserNotifications["This Week"][0] = [
							"replied_user_id" => $tableData["replied_user_id"],
							"notification_type_description" => $tableData["notification_type_description"],
							"type_action" => $tableData["type_action"],
							"replied_user_id" => $tableData["replied_user_id"],
							"threadUrl" => $tableData["threadUrl"]
						];
					} else {
						array_push($UserNotifications["This Week"], [
							"replied_user_id" => $tableData["replied_user_id"],
							"notification_type_description" => $tableData["notification_type_description"],
							"type_action" => $tableData["type_action"],
							"replied_user_id" => $tableData["replied_user_id"],
							"threadUrl" => $tableData["threadUrl"]
						]);
					}
				} else {
					if (!array_key_exists("Long Time Ago", $UserNotifications)) {
						$UserNotifications["Long Time Ago"][0] = [
							"replied_user_id" => $tableData["replied_user_id"],
							"notification_type_description" => $tableData["notification_type_description"],
							"type_action" => $tableData["type_action"],
							"replied_user_id" => $tableData["replied_user_id"],
							"threadUrl" => $tableData["threadUrl"]
						];
					} else {
						array_push($UserNotifications["Long Time Ago"], [
							"replied_user_id" => $tableData["replied_user_id"],
							"notification_type_description" => $tableData["notification_type_description"],
							"type_action" => $tableData["type_action"],
							"replied_user_id" => $tableData["replied_user_id"],
							"threadUrl" => $tableData["threadUrl"]
						]);
					}
				}
			}
			mysqli_close($dbConnection);
			return $UserNotifications;
		}
	
		public function post(array $parameters) : array {
			return array();
		}
	
		public function updateData(array $parameters) : array {
			return array();
		}
	
		public function discard(array $parameters) : array {
			return array();
		}
	
		public function getById(int $id) : array {
			return array();
		}
	
		public function getAll(array $parameters) : array {
		}
	}
?>
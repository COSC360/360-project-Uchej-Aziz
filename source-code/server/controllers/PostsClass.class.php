<?php
@session_start();
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/settings/ConnectDB.class.php';
require_once $_SERVER["DOCUMENT_ROOT"] . '/server/controllers/CommentsClass.class.php';

class PostsClass
{

    function get(array $inputs): array
    {
        return array();
    }

    function post(array $inputs): array
    {
        $sqlResult = array("response" => 400, "data" => array("message" => "Cannot create thread."));
        $dbCon = (new ConnectDB())->connect();

        $threadUrl = end($inputs);
        $idThread_query = "SELECT idThread, idUser from tblThreads WHERE link = '$threadUrl' AND isRowDeleted != 1";
        $sqlResult = mysqli_query($dbCon, $idThread_query);
        while ($row = mysqli_fetch_assoc($sqlResult)) {
            $idThread = $row["idThread"];
            $owner_id = $row["idUser"];
        }

        $get_user_query = "SELECT id FROM tblUsers WHERE username = '" . $_SESSION["USERNAME"] . "' LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $get_user_query);
        while ($row = mysqli_fetch_assoc($sqlResult)) {
            $user_id = $row["id"];
        }

        $caseNumber = (int)$inputs[0];
        switch ($caseNumber) {
            case 1:
                $sqlQuery = "INSERT INTO tblPosts(idUser, idThread, postTitle, content, image, link) 
						VALUES ($user_id, $idThread, '" . $inputs[1] . "', '" . $inputs[2] . "', '" . $inputs[3] . "', '" . $inputs[4] . "')";
                mysqli_query($dbCon, $sqlQuery);
                break;

            case 2:
                $sqlQuery = "INSERT INTO tblPosts(idUser, idThread, postTitle, content, image) 
						VALUES ($user_id, $idThread, '" . $inputs[1] . "', '" . $inputs[2] . "', '" . $inputs[3] . "')";
                mysqli_query($dbCon, $sqlQuery);
                break;
            case 3:
                $sqlQuery = "INSERT INTO tblPosts(idUser, idThread, postTitle, content, link) 
						VALUES ($user_id, $idThread, '" . $inputs[1] . "', '" . $inputs[2] . "', '" . $inputs[3] . "')";
                mysqli_query($dbCon, $sqlQuery);
                break;
            case 4:
                $sqlQuery = "INSERT INTO tblPosts(idUser, idThread, postTitle, image, link) 
						VALUES ($user_id, $idThread, '" . $inputs[1] . "', '" . $inputs[2] . "', '" . $inputs[3] . "')";
                mysqli_query($dbCon, $sqlQuery);
                break;
            case 5:
                $sqlQuery = "INSERT INTO tblPosts(idUser, idThread, postTitle, content) VALUES ($user_id, $idThread, '" . $inputs[1] . "', '" . $inputs[2] . "')";
                mysqli_query($dbCon, $sqlQuery);
                break;
            case 6:
                $sqlQuery = "INSERT INTO tblPosts(idUser, idThread, postTitle, image) VALUES ($user_id, $idThread, '" . $inputs[1] . "', '" . $inputs[2] . "')";
                mysqli_query($dbCon, $sqlQuery);
                break;
            case 7:
                $sqlQuery = "INSERT INTO tblPosts(idUser, idThread, postTitle, link) VALUES ($user_id, $idThread, '" . $inputs[1] . "', '" . $inputs[2] . "')";
                mysqli_query($dbCon, $sqlQuery);
                break;
        }

        $sqlQuery = "INSERT INTO tblNotifications (idUser,	idUserReply, notificationType, idThread) VALUES ($owner_id, $user_id, 1, $idThread)";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);

        mysqli_close($dbCon);
        return array("response" => 200);
    }

    function update(array $inputs): array
    {
        return array();
    }

    function delete(array $inputs): array
    {
        return array();
    }

    function doesPostExists(int $id): bool
    {
        $dbCon = (new ConnectDB())->connect();
        $sqlQuery = "SELECT idPost FROM tblPosts WHERE idPost = $id AND isRowHidden = 0 AND isRowDeleted = 0 LIMIT 1";
        $dbResult = mysqli_query($dbCon, $sqlQuery);
        while ($row = mysqli_fetch_assoc($dbResult)) {
            mysqli_close($dbCon);
            return true;
        }
        mysqli_close($dbCon);
        return false;
    }

    function findById(int $id): array
    {
        return array();
    }

    function commentStatus(array $inputs): array
    {
        $dbCon = (new ConnectDB())->connect();

        $sqlQuery = "SELECT id FROM tblUsers WHERE username='" . $_SESSION["USERNAME"] . "' LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);

        $user = mysqli_fetch_row($sqlResult);
        $userId = $user[0];
        if ($inputs[1] === "voteUp") {
            $sqlQuery = "SELECT idPost FROM tblPostVotes WHERE idPost = $inputs[0] AND idUser = $userId LIMIT 1";
            $dbResult = mysqli_query($dbCon, $sqlQuery);

            if (mysqli_num_rows($dbResult) === 0) {
                $sqlQuery = "INSERT INTO tblPostVotes VALUES($inputs[0], $userId, 1)";
                mysqli_query($dbCon, $sqlQuery);

                $sqlQuery = "SELECT idUser, idThread FROM tblPosts WHERE idPost = $inputs[0] LIMIT 1";
                $sqlResult = mysqli_query($dbCon, $sqlQuery);
                $p = mysqli_fetch_row($sqlResult);
                $postOwner = $p[0];
                $postThreadId = $p[1];
                $sqlQuery = "INSERT INTO tblNotifications(idUser, idUserReply, notificationType, idThread) VALUES($postOwner, $userId, 4, $postThreadId)";
                mysqli_query($dbCon, $sqlQuery);
            } else {
                $sqlQuery = "UPDATE tblPostVotes SET hasVote = 1 WHERE idPost = $inputs[0] AND idUser = $userId";
                mysqli_query($dbCon, $sqlQuery);
            }
        } else {
            $sqlQuery = "SELECT idPost FROM tblPostVotes WHERE idPost = $inputs[0] AND idUser = $userId LIMIT 1";
            $dbResult = mysqli_query($dbCon, $sqlQuery);

            if (mysqli_num_rows($dbResult) === 0) {
                $sqlQuery = "INSERT INTO tblPostVotes VALUES($inputs[0], $userId, 0)";
                mysqli_query($dbCon, $sqlQuery);

                $sqlQuery = "SELECT idUser, idThread FROM tblPosts WHERE idPost = $inputs[0] LIMIT 1";
                $sqlResult = mysqli_query($dbCon, $sqlQuery);
                $p = mysqli_fetch_row($sqlResult);
                $postOwner = $p[0];
                $postThreadId = $p[1];
                $sqlQuery = "INSERT INTO tblNotifications(idUser, idUserReply, notificationType, idThread) VALUES($postOwner, $userId, 3, $postThreadId)";
                mysqli_query($dbCon, $sqlQuery);
            } else {
                $sqlQuery = "UPDATE tblPostVotes SET hasVote = 0 WHERE idPost = $inputs[0] AND idUser = $userId";
                mysqli_query($dbCon, $sqlQuery);
            }
        }
        $sqlQuery = "SELECT 
		(SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
		FROM tblPosts LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost 
		WHERE tblPosts.isRowHidden = 0 AND tblPosts.isRowDeleted = 0 AND tblPosts.idPost = $inputs[0]";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);
        $p = mysqli_fetch_row($sqlResult);
        $voteCount = $p[0];

        mysqli_close($dbCon);
        return array("response" => 200, "numOfVotes" => $voteCount);
    }

    function getPostByQuery(string $inputQuery): array
    {
        $dbCon = (new ConnectDB())->connect();
        if (!isset($_SESSION['USERNAME'])) {
            $sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link as media_link, UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, (SELECT COUNT(tblComments.idPost) FROM tblComments WHERE tblComments.isRowDeleted=0 AND tblComments.idPost=tblPosts.idPost) as totalComments,
            CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = -1 AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
            IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = -1 AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
            (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
            FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost 
            WHERE tblPosts.isRowDeleted = 0 AND (tblPosts.postTitle LIKE '%$inputQuery%' OR tblPosts.content LIKE '%$inputQuery%')
            GROUP BY tblPosts.idPost ORDER BY numOfVotes DESC";

            $dbResult = mysqli_query($dbCon, $sqlQuery);

            $sqlResult = array();

            while ($row = mysqli_fetch_assoc($dbResult)) {
                $sqlResult[] = [
                    "idPost" => $row['idPost'],
                    "postTitle" => $row['postTitle'],
                    "content" => $row['content'],
                    "image" => $row['image'],
                    "media_link" => $row['media_link'],
                    "timestamp" => $row['createdFromNowInSeconds'],
                    "username" => $row['username'],
                    "ownerId" => $row['ownerId'],
                    "profile_image" => $row['profile_image'],
                    "link" => $row['link'],
                    "totalComments" => $row['totalComments'],
                    "isVoted" => 0,
                    "typeVote" => 0,
                    "numOfVotes" => $row['numOfVotes']
                ];
            }

            mysqli_close($dbCon);
            return $sqlResult;
        }

        $sqlQuery = "SELECT id FROM tblUsers WHERE username='" . $_SESSION["USERNAME"] . "' LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);

        $user = mysqli_fetch_row($sqlResult);
        $userId = $user[0];

        $sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link as media_link,UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, (SELECT COUNT(tblComments.idPost) FROM tblComments WHERE tblComments.isRowDeleted=0 AND tblComments.idPost=tblPosts.idPost) as totalComments,
		CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
		IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
		(SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
		FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost 
		WHERE tblPosts.isRowDeleted = 0 AND (tblPosts.postTitle LIKE '%$inputQuery%' OR tblPosts.content LIKE '%$inputQuery%')
		GROUP BY tblPosts.idPost ORDER BY numOfVotes DESC";
        $dbResult = mysqli_query($dbCon, $sqlQuery);

        $sqlResult = array();

        while ($row = mysqli_fetch_assoc($dbResult)) {
            $sqlResult[] = [
                "idPost" => $row['idPost'],
                "postTitle" => $row['postTitle'],
                "content" => $row['content'],
                "image" => $row['image'],
                "media_link" => $row['media_link'],
                "timestamp" => $row['createdFromNowInSeconds'],
                "username" => $row['username'],
                "ownerId" => $row['ownerId'],
                "profile_image" => $row['profile_image'],
                "link" => $row['link'],
                "totalComments" => $row['totalComments'],
                "isVoted" => $row['voted'],
                "typeVote" => $row['voteType'],
                "numOfVotes" => $row['numOfVotes']
            ];
        }
        mysqli_close($dbCon);
        return $sqlResult;
    }

    function searchPostByQueryInThread(array $inputs): array
    {
        $dbCon = (new ConnectDB())->connect();
        $inputQuery = $inputs[0];
        $threadUrl = $inputs[1];
        $sqlQuery = "SELECT tblThreads.idThread FROM tblThreads WHERE tblThreads.link = '$threadUrl' LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);
        while ($row = mysqli_fetch_assoc($sqlResult)) {
            $idThread = $row["idThread"];
        }

        $sqlQuery = "SELECT id FROM tblUsers WHERE username='" . $_SESSION["USERNAME"] . "' LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);

        $user = mysqli_fetch_row($sqlResult);
        $userId = $user[0];

        $sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link as media_link,UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, COUNT(tblComments.idPost) as totalComments, tblPosts.isRowHidden,
		CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
		IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
		(SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
		FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost 
		WHERE tblPosts.isRowDeleted = 0 AND (tblPosts.postTitle LIKE '%$inputQuery%' OR tblPosts.content LIKE '%$inputQuery%') AND tblPosts.idThread = $idThread
		GROUP BY tblPosts.idPost ORDER BY numOfVotes DESC";
        $dbResult = mysqli_query($dbCon, $sqlQuery);

        $sqlResult = array();

        while ($row = mysqli_fetch_assoc($dbResult)) {
            $sqlResult[] = [
                "idPost" => $row['idPost'],
                "postTitle" => $row['postTitle'],
                "content" => $row['content'],
                "image" => $row['image'],
                "media_link" => $row['media_link'],
                "timestamp" => $row['createdFromNowInSeconds'],
                "username" => $row['username'],
                "ownerId" => $row['ownerId'],
                "profile_image" => $row['profile_image'],
                "link" => $row['link'],
                "totalComments" => $row['totalComments'],
                "isVoted" => $row['voted'],
                "typeVote" => $row['voteType'],
                "numOfVotes" => $row['numOfVotes'],
                "isHidden" => $row['isRowHidden'],
                "isAdmin" => $_SESSION["IS_ADMIN"] == 1 ? true : false,
                "isOwner" => $_SESSION["USERNAME"] == $row['username'] ? true : false,
                "tblComments" => (new CommentsClass())->getAllCommentsFromPostId($row['idPost'], 0)
            ];
        }
        mysqli_close($dbCon);
        return $sqlResult;
    }

    function loadAllPosts(): array
    {
        $dbCon = (new ConnectDB())->connect();

        if (!isset($_SESSION['USERNAME'])) {
            $sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link as media_link,UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, (SELECT COUNT(tblComments.idPost) FROM tblComments WHERE tblComments.isRowDeleted=0 AND tblComments.idPost=tblPosts.idPost) as totalComments,
            CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = -1 AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
            IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = -1 AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
            (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
            FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost 
            WHERE tblPosts.isRowDeleted = 0 
            GROUP BY tblPosts.idPost ORDER BY numOfVotes DESC";
            $sqlResponse = mysqli_query($dbCon, $sqlQuery);

            $sqlResult = array();
            if ($sqlResponse) {
                while ($row = mysqli_fetch_assoc($sqlResponse)) {
                    $sqlResult[] = [
                        "idPost" => $row['idPost'],
                        "postTitle" => $row['postTitle'],
                        "content" => $row['content'],
                        "image" => $row['image'],
                        "media_link" => $row['media_link'],
                        "timestamp" => $row['createdFromNowInSeconds'],
                        "username" => $row['username'],
                        "ownerId" => $row['ownerId'],
                        "profile_image" => $row['profile_image'],
                        "link" => $row['link'],
                        "totalComments" => $row['totalComments'],
                        "isVoted" => 0,
                        "typeVote" => 0,
                        "numOfVotes" => $row['numOfVotes']
                    ];
                }
            }
            mysqli_close($dbCon);
            return $sqlResult;

        }

        $sqlQuery = "SELECT id FROM tblUsers WHERE username='" . $_SESSION["USERNAME"] . "' LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);

        $user = mysqli_fetch_row($sqlResult);
        $userId = $user[0];

        $sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link as media_link,UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, (SELECT COUNT(tblComments.idPost) FROM tblComments WHERE tblComments.isRowDeleted=0 AND tblComments.idPost=tblPosts.idPost) as totalComments,
		CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
		IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
		(SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
		FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost 
		WHERE tblPosts.isRowDeleted = 0 
		GROUP BY tblPosts.idPost ORDER BY numOfVotes DESC";
        $sqlResponse = mysqli_query($dbCon, $sqlQuery);

        $sqlResult = array();

        while ($row = mysqli_fetch_assoc($sqlResponse)) {
            $sqlResult[] = [
                "idPost" => $row['idPost'],
                "postTitle" => $row['postTitle'],
                "content" => $row['content'],
                "image" => $row['image'],
                "media_link" => $row['media_link'],
                "timestamp" => $row['createdFromNowInSeconds'],
                "username" => $row['username'],
                "ownerId" => $row['ownerId'],
                "profile_image" => $row['profile_image'],
                "link" => $row['link'],
                "totalComments" => $row['totalComments'],
                "isVoted" => $row['voted'],
                "typeVote" => $row['voteType'],
                "numOfVotes" => $row['numOfVotes']
            ];
        }
        mysqli_close($dbCon);
        return $sqlResult;
    }

    function loadPostByThread(array $inputs): array
    {
        $dbCon = (new ConnectDB())->connect();

        $sqlQuery = "SELECT id FROM tblUsers WHERE username='" . $_SESSION["USERNAME"] . "' LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);

        $user = mysqli_fetch_row($sqlResult);
        $userId = $user[0];
        $inputs[1] = "";
        if (!empty($inputs[1])) {
            if ($inputs[1] == "Top") {
                    $sqlQuery = "s§N tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost
				WHERE tblPosts.isRowDeleted = 0 AND tblThreads.link = '$inputs[0]'
				GROUP BY tblPosts.idPost ORDER BY numOfVotes DESC";
            }
            else if ($inputs[1] == "New") {
                $sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link as media_link,UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, (SELECT COUNT(tblComments.idPost) FROM tblComments WHERE tblComments.isRowDeleted=0 AND tblComments.idPost=tblPosts.idPost) as totalComments, tblPosts.isRowHidden,
				CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
				IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
				(SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
				FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost
				WHERE tblPosts.isRowDeleted = 0 AND tblThreads.link = '$inputs[0]'
				GROUP BY tblPosts.idPost ORDER BY createdFromNowInSeconds ASC";
            }
        } else {
            $sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link as media_link,UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, (SELECT COUNT(tblComments.idPost) FROM tblComments WHERE tblComments.isRowDeleted=0 AND tblComments.idPost=tblPosts.idPost) as totalComments, tblPosts.isRowHidden,
			CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
			IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
			(SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
			FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost
			WHERE tblPosts.isRowDeleted = 0 AND tblThreads.link = '$inputs[0]'
			GROUP BY tblPosts.idPost ORDER BY numOfVotes DESC";
        }



        $sqlResponse = mysqli_query($dbCon, $sqlQuery);


        $sqlResult = array();

        while ($row = mysqli_fetch_assoc($sqlResponse)) {
            $sqlResult[] = [
                "idPost" => $row['idPost'],
                "postTitle" => $row['postTitle'],
                "content" => $row['content'],
                "image" => $row['image'],
                "media_link" => $row['media_link'],
                "timestamp" => $row['createdFromNowInSeconds'],
                "username" => $row['username'],
                "ownerId" => $row['ownerId'],
                "profile_image" => $row['profile_image'],
                "link" => $row['link'],
                "totalComments" => $row['totalComments'],
                "isVoted" => $row['voted'],
                "typeVote" => $row['voteType'],
                "numOfVotes" => $row['numOfVotes'],
                "isHidden" => $row['isRowHidden'],
                "isAdmin" => $_SESSION["IS_ADMIN"] == 1 ? true : false,
                "isOwner" => $_SESSION["USERNAME"] == $row['username'] ? true : false,
                "comments" => (new CommentsClass())->getAllCommentsFromPostId($row['idPost'], 0)
            ];
            //$sqlResult["currentUserId"] = $userId;

        }
        mysqli_close($dbCon);
        return $sqlResult;
    }

    function loadSpecificPost(array $inputs): array
    {
        $dbCon = (new ConnectDB())->connect();
        $sqlQuery = "SELECT id FROM tblUsers WHERE username='" . $_SESSION["USERNAME"] . "' LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);
        $user = mysqli_fetch_row($sqlResult);
        $userId = $user[0];

        $sqlQuery = "SELECT tblPosts.idPost, tblPosts.postTitle, tblPosts.content, tblPosts.image, tblPosts.link as media_link,UNIX_TIMESTAMP(CURRENT_TIMESTAMP) - UNIX_TIMESTAMP(tblPosts.timestampCreated) as createdFromNowInSeconds, tblUsers.username, tblUsers.id as ownerId, tblUsers.profile_image, tblThreads.link, (SELECT COUNT(tblComments.idPost) FROM tblComments WHERE tblComments.isRowDeleted=0 AND tblComments.idPost=tblPosts.idPost) as totalComments, tblPosts.isRowHidden,
				CASE WHEN EXISTS(SELECT tblPostVotes.idUser FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost) THEN 1 ELSE 0 END as voted,
				IF ((SELECT tblPostVotes.hasVote FROM tblPostVotes WHERE tblPostVotes.idUser = $userId AND tblPosts.idPost = tblPostVotes.idPost AND tblPostVotes.hasVote = 1), 1, -1) as voteType,
				(SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 1 AND tblPosts.idPost = tblPostVotes.idPost) - (SELECT COUNT(*) FROM tblPostVotes WHERE tblPostVotes.hasVote = 0 AND tblPosts.idPost = tblPostVotes.idPost) as numOfVotes
				FROM tblPosts JOIN tblUsers ON tblPosts.idUser = tblUsers.id JOIN tblThreads ON tblThreads.idThread = tblPosts.idThread LEFT JOIN tblComments ON tblPosts.idPost = tblComments.idPost LEFT JOIN tblPostVotes ON tblPostVotes.idPost = tblPosts.idPost 
				WHERE tblPosts.isRowDeleted = 0 AND tblThreads.link = '$inputs[0]' AND tblPosts.idPost = $inputs[1]
				GROUP BY tblPosts.idPost";

        $sqlResponse = mysqli_query($dbCon, $sqlQuery);
        $sqlResult = array();

        while ($row = mysqli_fetch_assoc($sqlResponse)) {
            $sqlResult = [
                "idPost" => $row['idPost'],
                "postTitle" => $row['postTitle'],
                "content" => $row['content'],
                "image" => $row['image'],
                "media_link" => $row['media_link'],
                "timestamp" => $row['createdFromNowInSeconds'],
                "username" => $row['username'],
                "ownerId" => $row['ownerId'],
                "profile_image" => $row['profile_image'],
                "link" => $row['link'],
                "totalComments" => $row['totalComments'],
                "isVoted" => $row['voted'],
                "typeVote" => $row['voteType'],
                "numOfVotes" => $row['numOfVotes'],
                "isHidden" => $row['isRowHidden'],
                "isAdmin" => $_SESSION["IS_ADMIN"] == 1 ? true : false,
                "isOwner" => $_SESSION["USERNAME"] == $row['username'],
                "comments" => (new CommentsClass())->getAllCommentsFromPostId($row['idPost'], 1)


            ];

            $sqlResult["currentUserId"] = $userId;
        }
        mysqli_close($dbCon);
        return $sqlResult;
    }

    function findAll(array $inputs): array
    {

        $sqlResult = array(
            "response" => 400,
            "data" => array()
        );

        if ($_SERVER['REQUEST_METHOD'] == "GET") {

            $sqlResult["response"] = 200;
            return $sqlResult;
        }

        return $sqlResult;
    }

    function deletePost(array $inputs): array
    {
        $dbCon = (new ConnectDB())->connect();
        $sqlQuery = "UPDATE tblPosts SET isRowDeleted=1 WHERE tblPosts.idPost=$inputs[0] LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);

        $sqlQuery = "SELECT idUser , idThread FROM tblPosts WHERE idPost = $inputs[0] LIMIT 1";
        $sqlResponse = mysqli_query($dbCon, $sqlQuery);
        $row = mysqli_fetch_assoc($sqlResponse);
        $userId = $row['idUser'];
        $threadId = $row['idThread'];

        $get_user_query = "SELECT id FROM tblUsers WHERE username = '" . $_SESSION["USERNAME"] . "' LIMIT 1";
        $sqlResult = mysqli_query($dbCon, $get_user_query);
        while ($row = mysqli_fetch_assoc($sqlResult)) {
            $repliedUserId = $row["id"];
        }

        $sqlQuery = "INSERT INTO tblNotifications (idUser,	idUserReply, notificationType, idThread) VALUES ($userId, $repliedUserId, 5, $threadId)";
        $sqlResult = mysqli_query($dbCon, $sqlQuery);

        mysqli_close($dbCon);
        return array("response" => 200);
    }

    function disablePost(array $inputs): array
    {
        $dbCon = (new ConnectDB())->connect();

        if ($inputs[1] == "hide") {
            $sqlQuery = "UPDATE tblPosts SET isRowHidden = 1 WHERE tblPosts.idPost=$inputs[0] LIMIT 1";
            $changeButtonText = "Unhide";
            $sqlResult = mysqli_query($dbCon, $sqlQuery);

            $sqlQuery = "UPDATE tblComments SET isRowHidden = 1 WHERE tblComments.idPost = $inputs[0]";
            $sqlResult = mysqli_query($dbCon, $sqlQuery);
        } else {
            $sqlQuery = "UPDATE tblPosts SET isRowHidden = 0 WHERE tblPosts.idPost=$inputs[0] LIMIT 1";
            $changeButtonText = "Hide";
            $sqlResult = mysqli_query($dbCon, $sqlQuery);

            $sqlQuery = "UPDATE tblComments SET isRowHidden = 0 WHERE tblComments.idPost = $inputs[0]";
            $sqlResult = mysqli_query($dbCon, $sqlQuery);
        }

        mysqli_close($dbCon);
        return array("response" => 200, "changeButtonText" => $changeButtonText);
    }
}

?>
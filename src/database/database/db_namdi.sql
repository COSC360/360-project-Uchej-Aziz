
CREATE TABLE `comments` (
  `commentId` int NOT NULL,
  `related_post_id` int NOT NULL,
  `related_thread_id` int NOT NULL,
  `userId` int NOT NULL,
  `is_confidential` tinyint(1) NOT NULL DEFAULT '0',
  `is_refracted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `body` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `recorded_votes_comments` (
  `commentId` int NOT NULL,
  `userId` int NOT NULL,
  `vote` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




CREATE TABLE `UserNotifications` (
  `notification_id` int NOT NULL,
  `userId` int NOT NULL,
  `replied_user_id` int NOT NULL,
  `action` int NOT NULL,
  `related_thread_id` int NOT NULL,
  `timestamp_submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




CREATE TABLE `NotificationTypes` (
  `notification_type_id` int NOT NULL,
  `notification_type_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `NotificationTypes` (`notification_type_id`, `notification_type_description`) VALUES
(1, 'created a new post in your thread'),
(2, 'replied to your comment in a thread you are following'),
(3, 'downvoted your post in a thread you are following'),
(4, 'upvoted your post in a thread you are following'),
(5, 'removed your post in a thread you are following'),
(6, 'removed your thread that you started');

CREATE TABLE `OriginalPosts` (
  `related_post_id` int NOT NULL,
  `userId` int NOT NULL,
  `related_thread_id` int NOT NULL,
  `post_title` varchar(75) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `post_body` text,
  `post_image` varchar(256) DEFAULT NULL,
  `is_confidential` tinyint(1) NOT NULL DEFAULT '0',
  `is_refracted` tinyint(1) NOT NULL DEFAULT '0',
  `post_media_url` varchar(256) DEFAULT NULL,
  `timestamp_submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `PostVotes` (
  `related_post_id` int NOT NULL,
  `userId` int NOT NULL,
  `votes` tinyint(1) DEFAULT NULL COMMENT 'TRUE = vote up, FALSE = vote down'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `forum_threads` (
  `related_thread_id` int NOT NULL,
  `thread_title` varchar(25) NOT NULL,
  `threadUrl` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `background_image` varchar(256) NOT NULL,
  `thread_image` varchar(256) NOT NULL,
  `ownerId` int NOT NULL,
  `is_disabled` tinyint(1) NOT NULL DEFAULT '0',
  `is_refracted` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp_submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE `tokens` (
  `tokenId` int NOT NULL,
  `token_string` varchar(256) NOT NULL,
  `session_code` int DEFAULT NULL,
  `timestamp_submitted` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_datetime` datetime NOT NULL,
  `userId` int NOT NULL,
  `is_confirmation_token` tinyint(1) DEFAULT '1' COMMENT 'FALSE = restore'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



CREATE TABLE `users` (
  `userId` int NOT NULL,
  `username` varchar(10) NOT NULL,
  `email_address` varchar(25) NOT NULL,
  `is_email_verified` tinyint(1) DEFAULT '0',
  `password_hash` varchar(512) NOT NULL,
  `avatar_image_url` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'default.png',
  `password_salt` varchar(10) NOT NULL,
  `timestamp_submitted` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_account_disabled` tinyint(1) DEFAULT '0',
  `is_admin` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `userThreads` (
  `related_thread_id` int NOT NULL,
  `userId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


ALTER TABLE `comments`
  ADD PRIMARY KEY (`commentId`),
  ADD KEY `commentPostId` (`related_post_id`),
  ADD KEY `commentThreadId` (`related_thread_id`),
  ADD KEY `comments_user_id` (`userId`);


ALTER TABLE `recorded_votes_comments`
  ADD PRIMARY KEY (`commentId`,`userId`),
  ADD KEY `voter_user_id` (`userId`);


ALTER TABLE `UserNotifications`
  ADD PRIMARY KEY (`notification_id`,`userId`,`replied_user_id`),
  ADD KEY `notificationUserId` (`userId`),
  ADD KEY `notificationRepliedUserId` (`replied_user_id`),
  ADD KEY `notificationAction` (`action`),
  ADD KEY `notifications_thread_id` (`related_thread_id`);


ALTER TABLE `NotificationTypes`
  ADD PRIMARY KEY (`notification_type_id`);


ALTER TABLE `OriginalPosts`
  ADD PRIMARY KEY (`related_post_id`),
  ADD KEY `postThreadId` (`related_thread_id`),
  ADD KEY `postUserId` (`userId`);


ALTER TABLE `PostVotes`
  ADD PRIMARY KEY (`related_post_id`,`userId`),
  ADD KEY `postVotesUserId` (`userId`);

ALTER TABLE `forum_threads`
  ADD PRIMARY KEY (`related_thread_id`),
  ADD UNIQUE KEY `threadTitleIndex` (`thread_title`),
  ADD UNIQUE KEY `threadThreadUrlIndex` (`threadUrl`),
  ADD KEY `threadsOwnerId` (`ownerId`);


ALTER TABLE `tokens`
  ADD PRIMARY KEY (`tokenId`),
  ADD UNIQUE KEY `tokensTokenIndex` (`token_string`),
  ADD KEY `tokensUserId` (`userId`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email_address`);

ALTER TABLE `userThreads`
  ADD PRIMARY KEY (`related_thread_id`,`userId`),
  ADD KEY `userThreadsId` (`userId`);


ALTER TABLE `comments`
  MODIFY `commentId` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `UserNotifications`
  MODIFY `notification_id` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `NotificationTypes`
  MODIFY `notification_type_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;


ALTER TABLE `OriginalPosts`
  MODIFY `related_post_id` int NOT NULL AUTO_INCREMENT;



ALTER TABLE `forum_threads`
  MODIFY `related_thread_id` int NOT NULL AUTO_INCREMENT;


ALTER TABLE `tokens`
  MODIFY `tokenId` int NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `userId` int NOT NULL AUTO_INCREMENT;


ALTER TABLE `comments`
  ADD CONSTRAINT `commentPostId` FOREIGN KEY (`related_post_id`) REFERENCES `posts` (`related_post_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `commentThreadId` FOREIGN KEY (`related_thread_id`) REFERENCES `forum_threads` (`related_thread_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_user_id` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON UPDATE CASCADE;

ALTER TABLE `recorded_votes_comments`
  ADD CONSTRAINT `commentVotesCommentId` FOREIGN KEY (`commentId`) REFERENCES `comments` (`commentId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `voter_user_id` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON UPDATE CASCADE;

ALTER TABLE `UserNotifications`
  ADD CONSTRAINT `notificationAction` FOREIGN KEY (`action`) REFERENCES `NotificationTypes` (`notification_type_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `notificationRepliedUserId` FOREIGN KEY (`replied_user_id`) REFERENCES `users` (`userId`) ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_thread_id` FOREIGN KEY (`related_thread_id`) REFERENCES `forum_threads` (`related_thread_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `notificationUserId` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON UPDATE CASCADE;

ALTER TABLE `posts`
  ADD CONSTRAINT `postThreadId` FOREIGN KEY (`related_thread_id`) REFERENCES `forum_threads` (`related_thread_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `postUserId` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON UPDATE CASCADE;

ALTER TABLE `PostVotes`
  ADD CONSTRAINT `postVotesUserId` FOREIGN KEY (`related_post_id`) REFERENCES `posts` (`related_post_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `postVotesUserId` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON UPDATE CASCADE;

ALTER TABLE `forum_threads`
  ADD CONSTRAINT `threadsOwnerId` FOREIGN KEY (`ownerId`) REFERENCES `users` (`userId`) ON UPDATE CASCADE;

ALTER TABLE `tokens`
  ADD CONSTRAINT `tokensUserId` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON UPDATE CASCADE;

ALTER TABLE `userThreads`
  ADD CONSTRAINT `user_threads_thread_id` FOREIGN KEY (`related_thread_id`) REFERENCES `forum_threads` (`related_thread_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `userThreadsId` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;
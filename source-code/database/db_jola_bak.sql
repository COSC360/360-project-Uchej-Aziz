-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Generation Time: Nov 11, 2021 at 12:58 AM
-- Server version: 8.0.27
-- PHP Version: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_jola`
--

-- --------------------------------------------------------

--
-- Table structure for table `tblComments`
--

CREATE TABLE `tblComments` (
  `idComment` int NOT NULL,
  `idPost` int NOT NULL,
  `idThread` int NOT NULL,
  `idUser` int NOT NULL,
  `isRowHidden` tinyint(1) NOT NULL DEFAULT '0',
  `isRowDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestampCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblCommentVotes`
--

CREATE TABLE `tblCommentVotes` (
  `idComment` int NOT NULL,
  `idUser` int NOT NULL,
  `vote` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblNotifications`
--

CREATE TABLE `tblNotifications` (
  `id` int NOT NULL,
  `idUser` int NOT NULL,
  `idUserReply` int NOT NULL,
  `notificationType` int NOT NULL,
  `idThread` int NOT NULL,
  `timestampCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblNotificationEnum`
--

CREATE TABLE `tblNotificationEnum` (
  `id` int NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tblNotificationEnum`
--

INSERT INTO `tblNotificationEnum` (`id`, `content`) VALUES
(1, 'posted in your thread'),
(2, 'commented in your thread'),
(3, 'gave you a downvote'),
(4, 'gave you an upvote'),
(5, 'deleted your post'),
(6, 'deleted a thread you created');

-- --------------------------------------------------------

--
-- Table structure for table `tblPosts`
--

CREATE TABLE `tblPosts` (
  `idPost` int NOT NULL,
  `idUser` int NOT NULL,
  `idThread` int NOT NULL,
  `postTitle` varchar(75) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `content` text,
  `image` varchar(256) DEFAULT NULL,
  `isRowHidden` tinyint(1) NOT NULL DEFAULT '0',
  `isRowDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `link` varchar(256) DEFAULT NULL,
  `timestampCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblPostVotes`
--

CREATE TABLE `tblPostVotes` (
  `idPost` int NOT NULL,
  `idUser` int NOT NULL,
  `hasVote` tinyint(1) DEFAULT NULL COMMENT 'TRUE = vote up, FALSE = vote down'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblThreads`
--

CREATE TABLE `tblThreads` (
  `idThread` int NOT NULL,
  `title` varchar(25) NOT NULL,
  `link` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `bgImage` varchar(256) NOT NULL,
  `image` varchar(256) NOT NULL,
  `idUser` int NOT NULL,
  `isRowHidden` tinyint(1) NOT NULL DEFAULT '0',
  `isRowDeleted` tinyint(1) NOT NULL DEFAULT '0',
  `timestampCreated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblTokens`
--

CREATE TABLE `tblTokens` (
  `id` int NOT NULL,
  `code` varchar(256) NOT NULL,
  `keyCode` int DEFAULT NULL,
  `timestampCreated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `endDate` datetime NOT NULL,
  `idUser` int NOT NULL,
  `isUserConfirmed` tinyint(1) DEFAULT '1' COMMENT 'FALSE = restore'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblUsers`
--

CREATE TABLE `tblUsers` (
  `id` int NOT NULL,
  `username` varchar(10) NOT NULL,
  `email` varchar(25) NOT NULL,
  `isUserConfirmed` tinyint(1) DEFAULT '0',
  `password` varchar(512) NOT NULL,
  `profile_image` varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'default.png',
  `salt` varchar(10) NOT NULL,
  `timestampCreated` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '0',
  `adminStatus` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
-- --------------------------------------------------------

--
-- Table structure for table `tblUserThreads`
--

CREATE TABLE `tblUserThreads` (
  `idThread` int NOT NULL,
  `idUser` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblComments`
--
ALTER TABLE `tblComments`
  ADD PRIMARY KEY (`idComment`),
  ADD KEY `comments_idPost` (`idPost`),
  ADD KEY `comments_idThread` (`idThread`),
  ADD KEY `comments_idUser` (`idUser`);

--
-- Indexes for table `tblCommentVotes`
--
ALTER TABLE `tblCommentVotes`
  ADD PRIMARY KEY (`idComment`,`idUser`),
  ADD KEY `comment_votes_idUser` (`idUser`);

--
-- Indexes for table `tblNotifications`
--
ALTER TABLE `tblNotifications`
  ADD PRIMARY KEY (`id`,`idUser`,`idUserReply`),
  ADD KEY `notifications_idUser` (`idUser`),
  ADD KEY `notifications_idUserReply` (`idUserReply`),
  ADD KEY `notifications_action_type` (`notificationType`),
  ADD KEY `notifications_idThread` (`idThread`);

--
-- Indexes for table `tblNotificationEnum`
--
ALTER TABLE `tblNotificationEnum`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblPosts`
--
ALTER TABLE `tblPosts`
  ADD PRIMARY KEY (`idPost`),
  ADD KEY `idPostThread` (`idThread`),
  ADD KEY `idPostUser` (`idUser`);

--
-- Indexes for table `tblPostVotes`
--
ALTER TABLE `tblPostVotes`
  ADD PRIMARY KEY (`idPost`,`idUser`),
  ADD KEY `post_votes_idUser` (`idUser`);

--
-- Indexes for table `tblThreads`
--
ALTER TABLE `tblThreads`
  ADD PRIMARY KEY (`idThread`),
  ADD UNIQUE KEY `thread_title_index` (`title`),
  ADD UNIQUE KEY `thread_link_index` (`link`),
  ADD KEY `threads_owner_id` (`idUser`);

--
-- Indexes for table `tblTokens`
--
ALTER TABLE `tblTokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tokens_token_index` (`code`),
  ADD KEY `tokens_idUser` (`idUser`);

--
-- Indexes for table `tblUsers`
--
ALTER TABLE `tblUsers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tblUserThreads`
--
ALTER TABLE `tblUserThreads`
  ADD PRIMARY KEY (`idThread`,`idUser`),
  ADD KEY `user_threads_idUser` (`idUser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblComments`
--
ALTER TABLE `tblComments`
  MODIFY `idComment` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblNotifications`
--
ALTER TABLE `tblNotifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblNotificationEnum`
--
ALTER TABLE `tblNotificationEnum`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblPosts`
--
ALTER TABLE `tblPosts`
  MODIFY `idPost` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblThreads`
--
ALTER TABLE `tblThreads`
  MODIFY `idThread` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblTokens`
--
ALTER TABLE `tblTokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tblUsers`
--
ALTER TABLE `tblUsers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblComments`
--
ALTER TABLE `tblComments`
  ADD CONSTRAINT `comments_idPost` FOREIGN KEY (`idPost`) REFERENCES `tblPosts` (`idPost`) ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_idThread` FOREIGN KEY (`idThread`) REFERENCES `tblThreads` (`idThread`) ON UPDATE CASCADE,
  ADD CONSTRAINT `comments_idUser` FOREIGN KEY (`idUser`) REFERENCES `tblUsers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblCommentVotes`
--
ALTER TABLE `tblCommentVotes`
  ADD CONSTRAINT `comment_votes_idComment` FOREIGN KEY (`idComment`) REFERENCES `tblComments` (`idComment`) ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_votes_idUser` FOREIGN KEY (`idUser`) REFERENCES `tblUsers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblNotifications`
--
ALTER TABLE `tblNotifications`
  ADD CONSTRAINT `notifications_action_type` FOREIGN KEY (`notificationType`) REFERENCES `tblNotificationEnum` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_idUserReply` FOREIGN KEY (`idUserReply`) REFERENCES `tblUsers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_idThread` FOREIGN KEY (`idThread`) REFERENCES `tblThreads` (`idThread`) ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_idUser` FOREIGN KEY (`idUser`) REFERENCES `tblUsers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblPosts`
--
ALTER TABLE `tblPosts`
  ADD CONSTRAINT `idPostThread` FOREIGN KEY (`idThread`) REFERENCES `tblThreads` (`idThread`) ON UPDATE CASCADE,
  ADD CONSTRAINT `idPostUser` FOREIGN KEY (`idUser`) REFERENCES `tblUsers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblPostVotes`
--
ALTER TABLE `tblPostVotes`
  ADD CONSTRAINT `post_votes_idPost` FOREIGN KEY (`idPost`) REFERENCES `tblPosts` (`idPost`) ON UPDATE CASCADE,
  ADD CONSTRAINT `post_votes_idUser` FOREIGN KEY (`idUser`) REFERENCES `tblUsers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblThreads`
--
ALTER TABLE `tblThreads`
  ADD CONSTRAINT `threads_owner_id` FOREIGN KEY (`idUser`) REFERENCES `tblUsers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblTokens`
--
ALTER TABLE `tblTokens`
  ADD CONSTRAINT `tokens_idUser` FOREIGN KEY (`idUser`) REFERENCES `tblUsers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tblUserThreads`
--
ALTER TABLE `tblUserThreads`
  ADD CONSTRAINT `user_threads_idThread` FOREIGN KEY (`idThread`) REFERENCES `tblThreads` (`idThread`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `user_threads_idUser` FOREIGN KEY (`idUser`) REFERENCES `tblUsers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
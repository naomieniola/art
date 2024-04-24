-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2024 at 08:01 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `login_signup`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `discussion_id` int(11) DEFAULT NULL,
  `user_fullname` varchar(255) DEFAULT NULL,
  `comment_text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `discussion_id`, `user_fullname`, `comment_text`, `created_at`) VALUES
(177, NULL, 'Naomi Adesiyan', 'hi', '2024-03-15 16:30:21'),
(239, 158, 'nope', '@Naomi I only began to take an interest in art very recently. I\'ve been taking classes. It definitely is amazing!', '2024-04-20 12:15:29'),
(240, 158, 'Hannah', '@Naomi I only began to take an interest in art recently. I\'ve been taking lessons. I do think it i.', '2024-04-20 12:16:54'),
(257, 178, 'Hannah', 'dsdsdsdsd', '2024-04-23 13:47:40');

-- --------------------------------------------------------

--
-- Table structure for table `discussions`
--

CREATE TABLE `discussions` (
  `id` int(11) NOT NULL,
  `user_fullname` varchar(255) NOT NULL,
  `discussion_topic` varchar(255) NOT NULL,
  `thoughts` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussions`
--

INSERT INTO `discussions` (`id`, `user_fullname`, `discussion_topic`, `thoughts`, `created_at`) VALUES
(158, 'Naomi Adesiyan', 'What does art mean to you?', 'Thoughts?', '2024-03-05 01:03:21'),
(177, 'nope', 'What are some lesser-known artists or art movements that you believe deserve more recognition?', 'Any comments will be helpful! :)', '2024-04-18 13:22:48'),
(178, 'nope', 'Would you consider coding a form of art?', 'Just as an artist uses a paintbrush or sculpting tools to create, a coder uses programming languages to build digital creations. So maybe coders are artists too!', '2024-04-18 13:23:53');

-- --------------------------------------------------------

--
-- Table structure for table `liked_items`
--

CREATE TABLE `liked_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `liked_items`
--

INSERT INTO `liked_items` (`id`, `user_id`, `item_id`, `item_name`, `image_url`) VALUES
(243, 49, 3, 'Vincent Van Gogh', '/planetart/images/butterflies-and-poppies.jpg'),
(248, 49, 5, 'Tylor Hurd', '/planetart/images/tylor1.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `replies`
--

CREATE TABLE `replies` (
  `id` int(11) NOT NULL,
  `discussion_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_fullname` varchar(255) NOT NULL,
  `reply_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `reported_by_user_fullname` varchar(255) DEFAULT NULL,
  `reported_user_fullname` varchar(255) DEFAULT NULL,
  `report_comment_text` text DEFAULT NULL,
  `report_explanation` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin` int(11) DEFAULT 0,
  `remember_token` varchar(64) DEFAULT NULL,
  `remember_expires` bigint(20) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `full_name`, `username`, `password`, `admin`, `remember_token`, `remember_expires`, `profile_picture`, `date_of_birth`) VALUES
(3, 'testing@hotmail.com', 'testing', 'testing123', '$2y$10$3hA4zA/yQlKv4rO3uoVRzu4Y3BsHWKfScBdb9HJw6aIBf6jAJYZla', 0, NULL, NULL, '', NULL),
(18, 'rachel@g.com', 'Rachel', 'rachel123', '$2y$10$L.PhFA982DK6CVW0G8oLZOfiefuTHlEElWZMbcmtoKJ.nMMgHtBhW', 0, NULL, NULL, '', NULL),
(19, 'naomi@eniola.com', 'Naomi Eniola', 'naomieni', '$2y$10$ABUj/oau/r0lcf1Tl3ofO.qQn6PFeSOpXNpBGuLMDYQtwPh5lhxJq', 0, NULL, NULL, '', NULL),
(23, 'ssds@g.com', 'ssd', 'sds', '$2y$10$5IWKYA.njHro.kokAvbymu/QC8UjgUL9BffAiUeHSLt3HMtUKkE9u', 0, NULL, NULL, '', NULL),
(25, 'dfdf@dfdf.com', 'sadsds', 'sdsds', '$2y$10$wUAHNbrA8am2kUISRGDlEu6jhLnuroTBEp56dzCeWZBIlFZrybMmm', 1, NULL, NULL, '', NULL),
(26, 'fdfdf@g.com', 'dss', 'ght532', '$2y$10$WL.CVy8Dvj4wRaeTEk4L3.aNTIGVOffB.cZONgcOcW4i8tSLUDTYy', 0, NULL, NULL, '', NULL),
(29, 'cat@gmail.com', 'Cat', 'Cat', '$2y$10$YVdiXNQz/w9gy81HqMaLqu0gE2H6M2Rlx4sPydniGhlIIcudTeRX6', 0, NULL, NULL, '', NULL),
(30, 'jkjk@g.com', 'hello', 'hello', '$2y$10$6a1nV972tpf8quxRfrJiAuGRh.8vq5CwsL5nQrx8toNo5U4Gq1p7e', 1, NULL, NULL, '', NULL),
(35, 'hey123@gmail.com', 'hey', 'hey123', '$2y$10$fUuCWUXD6Pu2Wco2EWf9j.zZIB0bMsP8DpXMh6G4g2VLrIwpf8J.a', 0, NULL, NULL, '', NULL),
(36, 'hey12@gmail.com', 'heya', 'heya', '$2y$10$0DZPvQaQnWvAWZvbZiuxpOBWn7m/o8ILahE.a6UwaA0vXc1zlmotK', 0, NULL, NULL, '', NULL),
(37, 'bye@gmail.com', 'bye', 'bye123', '$2y$10$h/7A8vuFIVXAvum9m0A8sOd..AJLF0gT3MXsj/lpP6xYvgRh38dRi', 0, NULL, NULL, '', NULL),
(39, 'red123@gmail.com', 'Red', 'Red', '$2y$10$h3inJOujYcylKXfZUKCN0O/Zl6VBedj8giZ6tdSJB8ulgrjTMwfIm', 0, NULL, NULL, '', NULL),
(40, 'purple@gmail.com', 'purple', 'purple', '$2y$10$bEgVqhGFAxVByWx9UEHrxuggBYJrCZzI.ZtfaFdhheIqTatcblL3q', 0, NULL, NULL, '', NULL),
(47, 'goodbye@gmail.com', 'goodbye', 'goodbye', '$2y$10$Uw800JJQVSiaBkvdEnSbj.w4O3ovtlgQC.xHUQWV7mI8o89KwrIKO', 0, NULL, NULL, '', '2002-11-01'),
(48, 'hannah@gmail.com', 'hannah hello', 'hannah', '$2y$10$Fq7Ub6L3XcriD35IgaTNaO2XyLh6U0VOZ78Sejuyzegu8EE/5lLDK', 0, NULL, NULL, 'profile-user-black.png', '2002-04-09'),
(49, 'noe@gmail.com', 'Hannah', 'nope', '$2y$10$OHsgAq1GWHhwXICEl8TsLe5QL0389fbJUbUykg7NnLrvNjf/B.aNe', 1, NULL, NULL, 'profile-user-pink.png', '2003-11-01'),
(50, 'yes@gmail.com', 'yes', 'yes', '$2y$10$n2DCNfYiYHatAtFlTev.tOXL3jCeva8ttvyIxaME4Mj98gceVLUtW', 0, NULL, NULL, 'profile-user-green.png', '2002-10-01'),
(53, 'sfdfd@hot.com', 'fgfg fgfgfg', 'dfdfdfd', '$2y$10$siWphL3.5FeDLy6OD8M2NeJQ/VC7v7AY9aryjs8T9EtF32Ja/fTJa', 0, NULL, NULL, 'profile-user-pink.png', '2003-11-01'),
(55, 'sdsd@gmail.com', 'hi', 'hello123', '$2y$10$VBxzwzAjwVQbxiaMJRFGAOTUvGyxfLVeAbMrp.nKdc0PA47GKrG3y', 0, NULL, NULL, 'default-user.png', '2002-11-01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comments_discussion_id` (`discussion_id`);

--
-- Indexes for table `discussions`
--
ALTER TABLE `discussions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `liked_items`
--
ALTER TABLE `liked_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `liked_items_ibfk_1` (`user_id`);

--
-- Indexes for table `replies`
--
ALTER TABLE `replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discussion_id` (`discussion_id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reports_ibfk_1` (`comment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;

--
-- AUTO_INCREMENT for table `discussions`
--
ALTER TABLE `discussions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=195;

--
-- AUTO_INCREMENT for table `liked_items`
--
ALTER TABLE `liked_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `replies`
--
ALTER TABLE `replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comments_discussion_id` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`);

--
-- Constraints for table `liked_items`
--
ALTER TABLE `liked_items`
  ADD CONSTRAINT `liked_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `replies`
--
ALTER TABLE `replies`
  ADD CONSTRAINT `replies_ibfk_1` FOREIGN KEY (`discussion_id`) REFERENCES `discussions` (`id`),
  ADD CONSTRAINT `replies_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
